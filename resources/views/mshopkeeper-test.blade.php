<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MShopKeeper API Test Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.2em;
            opacity: 0.9;
        }
        
        .controls {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            text-align: center;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.3);
        }
        
        .btn.success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }
        
        .btn.error {
            background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        }
        
        .btn.loading {
            background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);
            cursor: not-allowed;
        }
        
        .status-panel {
            padding: 20px;
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            margin: 20px;
            border-radius: 0 8px 8px 0;
        }
        
        .status-panel.success {
            border-left-color: #28a745;
            background: #d4edda;
        }
        
        .status-panel.error {
            border-left-color: #dc3545;
            background: #f8d7da;
        }
        
        .config-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .config-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .config-card h3 {
            color: #667eea;
            margin-bottom: 15px;
            font-size: 1.2em;
        }
        
        .config-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .config-item:last-child {
            border-bottom: none;
        }
        
        .config-label {
            font-weight: 600;
            color: #495057;
        }
        
        .config-value {
            color: #6c757d;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        
        .results-container {
            padding: 20px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .result-card {
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }
        
        .result-header {
            background: #667eea;
            color: white;
            padding: 15px 20px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .result-header.success {
            background: #28a745;
        }
        
        .result-header.error {
            background: #dc3545;
        }
        
        .result-body {
            padding: 20px;
        }
        
        .json-viewer {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 1.8em;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #666;
            font-size: 12px;
        }
        
        .hidden {
            display: none;
        }
        
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .categories-tree {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
            max-height: 500px;
            overflow-y: auto;
        }

        .category-item {
            padding: 10px 5px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
            position: relative;
            border-radius: 5px;
            margin: 2px 0;
        }

        .category-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }

        .category-item:last-child {
            border-bottom: none;
        }
        
        .category-code {
            background: #e9ecef;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            margin-right: 8px;
            font-family: 'Courier New', monospace;
        }
        
        .category-status {
            font-size: 12px;
            padding: 2px 6px;
            border-radius: 4px;
            margin-left: 8px;
        }
        
        .active { background: #d4edda; color: #155724; }
        .inactive { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 MShopKeeper API Test Dashboard</h1>
            <p>Kiểm tra tích hợp API MShopKeeper - Vũ Phúc Bakery</p>
        </div>
        
        <div class="controls">
            <button class="btn" onclick="testAuthentication()">
                🔐 Test Authentication
            </button>
            <button class="btn" onclick="testCategories()">
                📋 Test Categories (Flat)
            </button>
            <button class="btn" onclick="testCategoriesTree()">
                🌳 Test Categories Tree
            </button>
            <button class="btn" onclick="testBranchs()">
                🏪 Test Branchs
            </button>
            <button class="btn" onclick="testCustomers()">
                👥 Test Customers
            </button>
            <button class="btn" onclick="testMemberLevels()">
                💎 Test Member Levels
            </button>

            <!-- Customer APIs -->
            <button class="btn" onclick="testCustomersByInfo()">
                🔍 Test Customers By Info
            </button>
            <button class="btn" onclick="testLomasCustomerSearch()">
                🎯 Test Lomas Customer Search
            </button>
            <button class="btn" onclick="testCustomersPointPaging()">
                📊 Test Customers Point Paging
            </button>

            <button class="btn" onclick="runFullTest()">
                🎯 Full Integration Test
            </button>
            <button class="btn" onclick="clearCache()">
                🗑️ Clear Cache
            </button>
            <button class="btn" onclick="loadConfig()">
                ⚙️ Load Config
            </button>
            <button class="btn" onclick="toggleMockMode()">
                🔄 Toggle Mock Mode
            </button>
        </div>
        
        <div id="status-panel" class="status-panel">
            <strong>📊 Trạng thái:</strong> Sẵn sàng test API MShopKeeper
        </div>
        
        <div id="config-info" class="config-info hidden"></div>
        
        <div id="results-container" class="results-container"></div>
    </div>

    <script>
        let currentConfig = {};
        let testResults = {};
        
        // Base URL cho API
        const API_BASE = window.location.origin;
        
        async function makeRequest(endpoint) {
            const response = await fetch(`${API_BASE}/test-mshopkeeper${endpoint}`);
            return await response.json();
        }
        
        function updateStatus(message, type = 'info') {
            const panel = document.getElementById('status-panel');
            panel.className = `status-panel ${type}`;
            panel.innerHTML = `<strong>📊 Trạng thái:</strong> ${message}`;
        }
        
        function addResult(title, data, success = true) {
            const container = document.getElementById('results-container');
            const resultId = `result-${Date.now()}`;
            
            const resultCard = document.createElement('div');
            resultCard.className = 'result-card';
            resultCard.innerHTML = `
                <div class="result-header ${success ? 'success' : 'error'}">
                    <span>${success ? '✅' : '❌'} ${title}</span>
                    <span>${new Date().toLocaleTimeString()}</span>
                </div>
                <div class="result-body">
                    ${generateResultContent(data)}
                </div>
            `;
            
            container.insertBefore(resultCard, container.firstChild);
        }
        
        function generateResultContent(data) {
            let content = '';
            
            // Stats nếu có
            if (data.execution_time_ms) {
                content += `
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number">${data.execution_time_ms}ms</div>
                            <div class="stat-label">Thời gian thực thi</div>
                        </div>
                        ${data.data && data.data.source ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.source.toUpperCase()}</div>
                            <div class="stat-label">Nguồn dữ liệu</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.categories_count ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.categories_count}</div>
                            <div class="stat-label">Số categories</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.tree_depth ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.tree_depth}</div>
                            <div class="stat-label">Độ sâu cây</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.root_categories ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.root_categories}</div>
                            <div class="stat-label">Categories gốc</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.branchs_count ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.branchs_count}</div>
                            <div class="stat-label">Chi nhánh</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.base_depot_count !== undefined ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.base_depot_count}</div>
                            <div class="stat-label">Kho tổng</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.total_customers ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.total_customers}</div>
                            <div class="stat-label">Tổng khách hàng</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.customers_count ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.customers_count}</div>
                            <div class="stat-label">KH trang này</div>
                        </div>
                        ` : ''}
                        ${data.data && data.data.total_member_levels ? `
                        <div class="stat-card">
                            <div class="stat-number">${data.data.total_member_levels}</div>
                            <div class="stat-label">Hạng thẻ</div>
                        </div>
                        ` : ''}
                    </div>
                `;
            }
            
            // Categories tree nếu có
            if (data.data && data.data.categories && Array.isArray(data.data.categories)) {
                content += generateCategoriesTree(data.data.categories);
            }

            // Categories tree (hierarchical) nếu có
            if (data.data && data.data.categories_tree) {
                content += generateCategoriesTreeHierarchical(data.data.categories_tree);
            }
            
            // JSON viewer
            content += `
                <div class="json-viewer">${JSON.stringify(data, null, 2)}</div>
            `;
            
            return content;
        }
        
        function generateCategoriesTree(categories) {
            let html = '<div class="categories-tree"><h4>📋 Cây danh mục Categories:</h4>';

            // Tạo map để dễ tìm kiếm
            const categoryMap = {};
            categories.forEach(cat => {
                categoryMap[cat.id] = { ...cat, children: [] };
            });

            // Xây dựng cây phân cấp
            const rootCategories = [];
            categories.forEach(cat => {
                if (cat.parent_id && categoryMap[cat.parent_id]) {
                    categoryMap[cat.parent_id].children.push(categoryMap[cat.id]);
                } else {
                    rootCategories.push(categoryMap[cat.id]);
                }
            });

            // Render cây
            html += renderCategoryLevel(rootCategories, 0);
            html += '</div>';
            return html;
        }

        function generateCategoriesTreeHierarchical(categoriesTree) {
            let html = '<div class="categories-tree"><h4>🌳 Cây danh mục Categories (Tree API):</h4>';

            // Render trực tiếp từ tree structure
            html += renderCategoryTreeLevel(categoriesTree, 0);
            html += '</div>';
            return html;
        }

        function renderCategoryTreeLevel(categories, level) {
            let html = '';
            const indent = level * 30; // 30px cho mỗi cấp

            categories.forEach(category => {
                const statusClass = category.Inactive ? 'inactive' : 'active';
                const statusText = category.Inactive ? 'Ngừng' : 'Hoạt động';
                const hasChildren = category.Children && category.Children.length > 0;
                const levelIcon = level === 0 ? '📁' : (hasChildren ? '📂' : '📄');
                const levelColor = level === 0 ? '#e74c3c' : (level === 1 ? '#3498db' : '#27ae60');

                html += `
                    <div class="category-item" style="margin-left: ${indent}px; border-left: ${level > 0 ? '2px solid #e9ecef' : 'none'}; padding-left: ${level > 0 ? '15px' : '0'};">
                        <span style="color: ${levelColor};">${levelIcon}</span>
                        <span class="category-code">${category.Code || category.Id}</span>
                        <strong style="color: ${levelColor};">${category.Name}</strong>
                        <span class="category-status ${statusClass}">${statusText}</span>
                        <small style="color: #666; margin-left: 10px;">Grade ${category.Grade}</small>
                        ${hasChildren ? `<small style="color: #666; margin-left: 10px;">(${category.Children.length} danh mục con)</small>` : ''}
                        ${category.Description ? `<br><small style="margin-left: ${indent + 40}px; color: #666;">💬 ${category.Description}</small>` : ''}
                    </div>
                `;

                // Render children recursively
                if (hasChildren) {
                    html += renderCategoryTreeLevel(category.Children, level + 1);
                }
            });

            return html;
        }

        function renderCategoryLevel(categories, level) {
            let html = '';
            const indent = level * 30; // 30px cho mỗi cấp

            categories.forEach(category => {
                const statusClass = category.status === 'active' ? 'active' : 'inactive';
                const statusText = category.status === 'active' ? 'Hoạt động' : 'Ngừng';
                const hasChildren = category.children && category.children.length > 0;
                const levelIcon = level === 0 ? '📁' : (hasChildren ? '📂' : '📄');
                const levelColor = level === 0 ? '#e74c3c' : (level === 1 ? '#3498db' : '#27ae60');

                html += `
                    <div class="category-item" style="margin-left: ${indent}px; border-left: ${level > 0 ? '2px solid #e9ecef' : 'none'}; padding-left: ${level > 0 ? '15px' : '0'};">
                        <span style="color: ${levelColor};">${levelIcon}</span>
                        <span class="category-code">${category.id}</span>
                        <strong style="color: ${levelColor};">${category.name}</strong>
                        <span class="category-status ${statusClass}">${statusText}</span>
                        ${hasChildren ? `<small style="color: #666; margin-left: 10px;">(${category.children.length} danh mục con)</small>` : ''}
                        ${category.description ? `<br><small style="margin-left: ${indent + 40}px; color: #666;">💬 ${category.description}</small>` : ''}
                    </div>
                `;

                // Render children recursively
                if (hasChildren) {
                    html += renderCategoryLevel(category.children, level + 1);
                }
            });

            return html;
        }
        
        async function testAuthentication() {
            updateStatus('🔄 Đang test authentication...', 'info');
            
            try {
                const result = await makeRequest('/auth');
                testResults.auth = result;
                
                if (result.success) {
                    updateStatus('✅ Authentication thành công!', 'success');
                    addResult('Authentication Test', result, true);
                } else {
                    updateStatus('❌ Authentication thất bại!', 'error');
                    addResult('Authentication Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Authentication Test', { error: error.message }, false);
            }
        }
        
        async function testCategories() {
            updateStatus('🔄 Đang test categories (flat)...', 'info');

            try {
                const result = await makeRequest('/categories');
                testResults.categories = result;

                if (result.success) {
                    updateStatus(`✅ Lấy categories (flat) thành công! (${result.data.categories_count} items)`, 'success');
                    addResult('Categories Flat Test', result, true);
                } else {
                    updateStatus('❌ Lấy categories (flat) thất bại!', 'error');
                    addResult('Categories Flat Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Categories Flat Test', { error: error.message }, false);
            }
        }

        async function testCategoriesTree() {
            updateStatus('🔄 Đang test categories tree...', 'info');

            try {
                const result = await makeRequest('/categories-tree');
                testResults.categoriesTree = result;

                if (result.success) {
                    const data = result.data;
                    updateStatus(`✅ Lấy categories tree thành công! (${data.categories_count} nodes, ${data.tree_depth} levels)`, 'success');
                    addResult('Categories Tree Test', result, true);
                } else {
                    updateStatus('❌ Lấy categories tree thất bại!', 'error');
                    addResult('Categories Tree Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Categories Tree Test', { error: error.message }, false);
            }
        }

        async function testBranchs() {
            updateStatus('🔄 Đang test branchs...', 'info');

            try {
                const result = await makeRequest('/branchs');
                testResults.branchs = result;

                if (result.success) {
                    const data = result.data;
                    updateStatus(`✅ Lấy branchs thành công! (${data.branchs_count} chi nhánh, ${data.base_depot_count} kho tổng)`, 'success');
                    addResult('Branchs Test', result, true);
                } else {
                    updateStatus('❌ Lấy branchs thất bại!', 'error');
                    addResult('Branchs Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Branchs Test', { error: error.message }, false);
            }
        }

        async function testCustomers() {
            updateStatus('🔄 Đang test customers...', 'info');

            try {
                const result = await makeRequest('/customers');
                testResults.customers = result;

                if (result.success) {
                    const data = result.data;
                    updateStatus(`✅ Lấy customers thành công! (${data.customers_count}/${data.total_customers} khách hàng)`, 'success');
                    addResult('Customers Test', result, true);
                } else {
                    updateStatus('❌ Lấy customers thất bại!', 'error');
                    addResult('Customers Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Customers Test', { error: error.message }, false);
            }
        }

        async function testMemberLevels() {
            updateStatus('🔄 Đang test member levels...', 'info');

            try {
                const result = await makeRequest('/member-levels');
                testResults.memberLevels = result;

                if (result.success) {
                    const data = result.data;
                    updateStatus(`✅ Lấy member levels thành công! (${data.member_levels_count}/${data.total_member_levels} hạng thẻ)`, 'success');
                    addResult('Member Levels Test', result, true);
                } else {
                    updateStatus('❌ Lấy member levels thất bại!', 'error');
                    addResult('Member Levels Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Member Levels Test', { error: error.message }, false);
            }
        }

        // ========================================
        // CUSTOMER APIs TEST FUNCTIONS
        // ========================================

        async function testCustomersByInfo() {
            updateStatus('🔄 Đang test customers by info...', 'info');

            try {
                // Test với số điện thoại mặc định
                const keySearch = prompt('Nhập SĐT hoặc Email để tìm kiếm:', '0987555222');
                if (!keySearch) return;

                const result = await makeRequest(`/customers-by-info?key_search=${encodeURIComponent(keySearch)}`);
                testResults.customersByInfo = result;

                if (result.success) {
                    const data = result.data;
                    updateStatus(`✅ Tìm kiếm khách hàng thành công! (${data.customers_count} khách hàng)`, 'success');
                    addResult('Customers By Info Test', result, true);
                } else {
                    updateStatus('❌ Tìm kiếm khách hàng thất bại!', 'error');
                    addResult('Customers By Info Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Customers By Info Test', { error: error.message }, false);
            }
        }

        async function testLomasCustomerSearch() {
            updateStatus('🔄 Đang test Lomas customer search...', 'info');

            try {
                // Test với số điện thoại mặc định
                const keyword = prompt('Nhập SĐT hoặc mã thẻ để tìm kiếm:', '0326643186');
                if (!keyword) return;

                const result = await makeRequest(`/customers-lomas-search?keyword=${encodeURIComponent(keyword)}`);
                testResults.lomasCustomerSearch = result;

                if (result.success) {
                    const data = result.data;
                    if (data.customer) {
                        updateStatus(`✅ Tìm thấy khách hàng Lomas: ${data.customer.FullName}`, 'success');
                    } else {
                        updateStatus('⚠️ Không tìm thấy khách hàng Lomas', 'warning');
                    }
                    addResult('Lomas Customer Search Test', result, true);
                } else {
                    updateStatus('❌ Tìm kiếm khách hàng Lomas thất bại!', 'error');
                    addResult('Lomas Customer Search Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Lomas Customer Search Test', { error: error.message }, false);
            }
        }

        async function testCustomersPointPaging() {
            updateStatus('🔄 Đang test customers point paging...', 'info');

            try {
                // Test với trang và limit mặc định
                const page = prompt('Nhập số trang (mặc định 1):', '1') || '1';
                const limit = prompt('Nhập số bản ghi mỗi trang (mặc định 10):', '10') || '10';

                const result = await makeRequest(`/customers-point-paging?page=${page}&limit=${limit}`);
                testResults.customersPointPaging = result;

                if (result.success) {
                    const data = result.data;
                    updateStatus(`✅ Lấy điểm khách hàng thành công! (${data.customer_points_count}/${data.total_customer_points} khách hàng)`, 'success');
                    addResult('Customers Point Paging Test', result, true);
                } else {
                    updateStatus('❌ Lấy điểm khách hàng thất bại!', 'error');
                    addResult('Customers Point Paging Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Customers Point Paging Test', { error: error.message }, false);
            }
        }
        
        async function runFullTest() {
            updateStatus('🔄 Đang chạy full integration test...', 'info');
            
            try {
                const result = await makeRequest('/full-test');
                testResults.fullTest = result;
                
                if (result.overall_success) {
                    updateStatus(`✅ Full test thành công! (${result.total_execution_time_ms}ms)`, 'success');
                    addResult('Full Integration Test', result, true);
                } else {
                    updateStatus('❌ Full test thất bại!', 'error');
                    addResult('Full Integration Test', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Full Integration Test', { error: error.message }, false);
            }
        }
        
        async function clearCache() {
            updateStatus('🔄 Đang xóa cache...', 'info');
            
            try {
                const result = await makeRequest('/clear-cache');
                
                if (result.success) {
                    updateStatus('✅ Cache đã được xóa thành công!', 'success');
                    addResult('Clear Cache', result, true);
                } else {
                    updateStatus('❌ Không thể xóa cache!', 'error');
                    addResult('Clear Cache', result, false);
                }
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Clear Cache', { error: error.message }, false);
            }
        }
        
        async function loadConfig() {
            updateStatus('🔄 Đang tải cấu hình...', 'info');
            
            try {
                const result = await makeRequest('');
                currentConfig = result.config;
                
                displayConfig(result);
                updateStatus('✅ Đã tải cấu hình thành công!', 'success');
                addResult('Load Configuration', result, true);
            } catch (error) {
                updateStatus(`❌ Lỗi kết nối: ${error.message}`, 'error');
                addResult('Load Configuration', { error: error.message }, false);
            }
        }
        
        function displayConfig(configData) {
            const container = document.getElementById('config-info');
            
            container.innerHTML = `
                <div class="config-card">
                    <h3>🔧 Cấu hình API</h3>
                    <div class="config-item">
                        <span class="config-label">App ID:</span>
                        <span class="config-value">${configData.config.app_id}</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Domain:</span>
                        <span class="config-value">${configData.config.domain}</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Base URL:</span>
                        <span class="config-value">${configData.config.base_url}</span>
                    </div>
                    <div class="config-item">
                        <span class="config-label">Timeout:</span>
                        <span class="config-value">${configData.config.timeout}s</span>
                    </div>
                </div>
                <div class="config-card">
                    <h3>🎯 Endpoints</h3>
                    ${Object.entries(configData.config.endpoints).map(([key, value]) => `
                        <div class="config-item">
                            <span class="config-label">${key}:</span>
                            <span class="config-value">${value}</span>
                        </div>
                    `).join('')}
                </div>
                <div class="config-card">
                    <h3>📋 Available Tests</h3>
                    ${Object.entries(configData.available_tests).map(([key, value]) => `
                        <div class="config-item">
                            <span class="config-label">${key}:</span>
                            <span class="config-value">${value}</span>
                        </div>
                    `).join('')}
                </div>
            `;
            
            container.classList.remove('hidden');
        }
        
        function toggleMockMode() {
            updateStatus('ℹ️ Mock mode toggle cần cấu hình trong .env file (MSHOPKEEPER_MOCK_MODE)', 'info');
            addResult('Mock Mode Info', {
                message: 'Để chuyển đổi mock mode, cập nhật MSHOPKEEPER_MOCK_MODE trong file .env',
                current_mode: 'Kiểm tra trong config',
                instructions: [
                    'MSHOPKEEPER_MOCK_MODE=true (sử dụng dữ liệu giả lập)',
                    'MSHOPKEEPER_MOCK_MODE=false (kết nối API thực tế)'
                ]
            }, true);
        }
        
        // Auto load config khi trang được tải
        window.onload = function() {
            loadConfig();
        };
    </script>
</body>
</html>
