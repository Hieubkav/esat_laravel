<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use App\Services\MShopKeeperCustomerAuthService;
use App\Services\PasswordResetService;
use Illuminate\Validation\ValidationException;

class ProfileManager extends Component
{
    public $name = '';
    public $address = '';
    public $email = '';
    public $phone = '';
    public $gender = '';
    public $identify_number = '';
    public $current_password = '';
    public $new_password = '';
    public $new_password_confirmation = '';
    public $activeTab = 'info'; // 'info' hoặc 'password'
    public $customer = null;

    public function mount()
    {
        $authService = app(MShopKeeperCustomerAuthService::class);
        $this->customer = $authService->user();

        if ($this->customer) {
            $this->name = $this->customer->name ?? '';
            $this->address = $this->customer->addr ?? '';
            $this->email = $this->customer->email ?? '';
            $this->phone = $this->customer->tel ?? '';
            $this->gender = $this->customer->gender ?? '';
            $this->identify_number = $this->customer->identify_number ?? '';
        }
    }

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ];

        if ($this->activeTab === 'password') {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min:6|confirmed';
        }

        return $rules;
    }

    protected $messages = [
        'name.required' => 'Vui lòng nhập họ và tên',
        'name.max' => 'Họ và tên không được quá 255 ký tự',
        'address.max' => 'Địa chỉ không được quá 500 ký tự',
        'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
        'new_password.required' => 'Vui lòng nhập mật khẩu mới',
        'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự',
        'new_password.confirmed' => 'Xác nhận mật khẩu không khớp',
    ];

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetValidation();
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function updateInfo()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        // Note: MShopKeeper customers không thể update info từ website
        // Chỉ hiển thị thông báo
        session()->flash('success', 'Thông tin khách hàng được quản lý bởi hệ thống MShopKeeper. Vui lòng liên hệ admin để cập nhật.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        $authService = app(MShopKeeperCustomerAuthService::class);
        $passwordResetService = app(PasswordResetService::class);
        $customer = $authService->user();

        $result = $passwordResetService->changePassword(
            $customer,
            $this->current_password,
            $this->new_password
        );

        if ($result['success']) {
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
            session()->flash('success', $result['message']);
        } else {
            throw ValidationException::withMessages([
                'current_password' => $result['message'],
            ]);
        }
    }

    public function render()
    {
        return view('livewire.customer.profile-manager');
    }
}
