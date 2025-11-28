<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealMaster Pro | Meal Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #e2136e;
            --primary-dark: #c10c5c;
            --primary-light: #f8e0ec;
            --secondary: #2d3748;
            --accent: #00d9c9;
            --success: #27ae60;
            --warning: #f39c12;
            --danger: #e74c3c;
            --light: #f8f9fa;
            --dark: #2d3748;
            --gray: #718096;
            --light-gray: #e2e8f0;
            --card-shadow: 0 10px 30px rgba(226, 19, 110, 0.15);
            --hover-shadow: 0 20px 40px rgba(226, 19, 110, 0.25);
            --gradient-primary: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            --gradient-success: linear-gradient(135deg, var(--success) 0%, #219653 100%);
            --gradient-accent: linear-gradient(135deg, var(--accent) 0%, #00b4a0 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e2e8f0 100%);
            color: var(--dark);
            font-family: 'Segoe UI', system-ui, -apple-system, 'Roboto', sans-serif;
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* App Container */
        .app-container {
            max-width: 100%;
            margin: 0 auto;
            padding: 0;
            min-height: 100vh;
        }

        /* Header - bKash Style */
        .app-header {
            background: var(--gradient-primary);
            padding: 20px 0;
            box-shadow: 0 4px 20px rgba(226, 19, 110, 0.3);
            position: relative;
            overflow: hidden;
        }

        .app-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            animation: float 20s infinite linear;
        }

        @keyframes float {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                transform: translate(-20px, -20px) rotate(360deg);
            }
        }

        .app-title {
            color: white;
            font-weight: 800;
            font-size: 1.8rem;
            margin-bottom: 5px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .app-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.95rem;
            font-weight: 400;
            opacity: 0.9;
        }

        .month-indicator {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            color: white;
            padding: 12px 20px;
            border-radius: 15px;
            font-weight: 700;
            font-size: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Main Content */
        .main-content {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* Control Panel - bKash Card Style */
        .control-panel {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 25px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .control-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .form-label {
            font-weight: 700;
            color: var(--secondary);
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-select,
        .form-control {
            border: 2px solid var(--light-gray);
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: white;
        }

        .form-select:focus,
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(226, 19, 110, 0.1);
            transform: translateY(-2px);
        }

        /* Enhanced Input Styling */
        .form-control:valid {
            border-color: var(--success);
        }

        .form-control:invalid {
            border-color: var(--danger);
        }

        .input-error-message {
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 4px;
            display: flex;
            align-items: center;
        }

        /* Quick input hints */
        .quick-input-hints {
            display: flex;
            gap: 8px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .quick-input-btn {
            padding: 4px 8px;
            border: 1px solid var(--light-gray);
            border-radius: 6px;
            background: white;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .quick-input-btn:hover {
            background: var(--primary-light);
            border-color: var(--primary);
        }

        /* Focus states for better UX */
        .form-control:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(226, 19, 110, 0.15);
        }

        /* Buttons - bKash Style */
        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            border-radius: 12px;
            padding: 14px 24px;
            font-weight: 700;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(226, 19, 110, 0.3);
            position: relative;
            overflow: hidden;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(226, 19, 110, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
            border-radius: 12px;
            padding: 14px 24px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(226, 19, 110, 0.3);
        }

        /* Stats Cards - Modern Design */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .stats-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--hover-shadow);
        }

        .stats-card.success::before {
            background: var(--gradient-success);
        }

        .stats-card.accent::before {
            background: var(--gradient-accent);
        }

        .stats-card.warning::before {
            background: linear-gradient(135deg, var(--warning) 0%, #e67e22 100%);
        }

        .stats-icon {
            font-size: 2.5rem;
            opacity: 0.1;
            position: absolute;
            right: 20px;
            bottom: 20px;
            color: var(--primary);
        }

        .stats-card.success .stats-icon {
            color: var(--success);
        }

        .stats-card.accent .stats-icon {
            color: var(--accent);
        }

        .stats-card.warning .stats-icon {
            color: var(--warning);
        }

        .stats-card .card-title {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--gray);
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stats-card .card-text {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 0;
        }

        /* Form Container */
        .meal-form-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 30px;
            margin-bottom: 25px;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .meal-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-success);
        }

        .form-section-title {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Tables */
        .data-container {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            padding: 25px;
            margin-bottom: 25px;
            border: none;
            position: relative;
        }

        .data-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-accent);
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .data-table th {
            background: var(--gradient-primary);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 700;
            font-size: 0.85rem;
            position: sticky;
            top: 0;
        }

        .data-table td {
            padding: 15px 12px;
            border-bottom: 1px solid var(--light-gray);
            transition: all 0.2s ease;
        }

        .data-table tr:hover td {
            background: var(--primary-light);
            transform: scale(1.01);
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-edit {
            background: var(--primary-light);
            color: var(--primary);
        }

        .btn-delete {
            background: #fee;
            color: var(--danger);
        }

        .btn-edit:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }

        .btn-delete:hover {
            background: var(--danger);
            color: white;
            transform: translateY(-2px);
        }

        /* Notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--gradient-success);
            color: white;
            padding: 15px 25px;
            border-radius: 50px;
            font-size: 14px;
            font-weight: bold;
            z-index: 10000;
            display: none;
            box-shadow: 0 6px 20px rgba(39, 174, 96, 0.4);
            border: none;
            animation: slideInRight 0.5s ease;
        }

        .notification.error {
            background: linear-gradient(135deg, var(--danger) 0%, #c0392b 100%);
        }

        .notification.warning {
            background: linear-gradient(135deg, var(--warning) 0%, #e67e22 100%);
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Custom Modal - Improved Design */
        .custom-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(5px);
            z-index: 1000;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background: white;
            border-radius: 20px;
            padding: 30px;
            max-width: 500px;
            width: 95%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            border: 2px solid var(--light-gray);
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translate(-50%, -40%);
                opacity: 0;
            }

            to {
                transform: translate(-50%, -50%);
                opacity: 1;
            }
        }

        .modal-header {
            border-bottom: 2px solid var(--light-gray);
            padding-bottom: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .modal-title {
            font-weight: 800;
            color: var(--danger);
            font-size: 1.4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .modal-body {
            padding: 10px 0;
        }

        .modal-footer {
            border-top: 2px solid var(--light-gray);
            padding-top: 20px;
            margin-top: 20px;
        }

        .delete-details {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 12px;
            padding: 15px;
            margin: 15px 0;
        }

        .delete-detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding: 5px 0;
        }

        .delete-detail-label {
            font-weight: 600;
            color: var(--gray);
        }

        .delete-detail-value {
            font-weight: 700;
            color: var(--dark);
        }

        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }

            .app-title {
                font-size: 1.4rem;
            }

            .month-indicator {
                font-size: 0.9rem;
                padding: 10px 15px;
            }

            .control-panel,
            .meal-form-container,
            .data-container {
                padding: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                gap: 15px;
            }

            .stats-card {
                padding: 20px;
            }

            .stats-card .card-text {
                font-size: 1.8rem;
            }

            .data-table {
                font-size: 0.8rem;
                display: block;
                overflow-x: auto;
            }

            .data-table th,
            .data-table td {
                padding: 10px 8px;
                white-space: nowrap;
            }

            .modal-content {
                padding: 20px;
                margin: 10px;
                width: calc(100% - 20px);
            }

            .form-section-title {
                font-size: 1.1rem;
            }

            .btn-primary,
            .btn-outline-primary {
                padding: 12px 20px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .app-title {
                font-size: 1.2rem;
            }

            .month-indicator {
                font-size: 0.8rem;
                padding: 8px 12px;
            }

            .control-panel,
            .meal-form-container,
            .data-container {
                padding: 15px;
            }

            .stats-card {
                padding: 15px;
            }

            .stats-card .card-text {
                font-size: 1.6rem;
            }

            .data-table {
                font-size: 0.75rem;
            }

            .modal-content {
                padding: 15px;
            }

            .modal-title {
                font-size: 1.2rem;
            }

            .delete-details {
                padding: 10px;
            }

            .action-btn {
                padding: 4px 8px;
                font-size: 0.7rem;
            }
        }

        @media (max-width: 360px) {
            .app-title {
                font-size: 1.1rem;
            }

            .main-content {
                padding: 10px;
            }

            .control-panel,
            .meal-form-container,
            .data-container {
                padding: 12px;
            }

            .stats-grid {
                gap: 10px;
            }

            .btn-primary,
            .btn-outline-primary {
                padding: 10px 15px;
                font-size: 0.8rem;
            }

            .data-table th,
            .data-table td {
                padding: 8px 6px;
            }
        }

        /* Floating Action Button */
        .floating-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--gradient-primary);
            color: white;
            border: none;
            font-size: 24px;
            box-shadow: 0 8px 25px rgba(226, 19, 110, 0.4);
            transition: all 0.3s ease;
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .floating-btn:hover {
            transform: scale(1.1) rotate(15deg);
            box-shadow: 0 12px 30px rgba(226, 19, 110, 0.6);
        }

        @media (max-width: 768px) {
            .floating-btn {
                width: 50px;
                height: 50px;
                font-size: 20px;
                bottom: 20px;
                right: 20px;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }

        /* Table responsive fixes */
        @media (max-width: 768px) {
            .table-responsive {
                border: 1px solid var(--light-gray);
                border-radius: 12px;
                overflow: hidden;
            }

            .data-table {
                min-width: 600px;
            }
        }

        /* Form responsive fixes */
        @media (max-width: 576px) {
            .row.g-3 {
                margin: 0 -5px;
            }

            .row.g-3>[class*="col-"] {
                padding: 0 5px;
            }
        }
    </style>
</head>

<body>
    <!-- Notification -->
    <div class="notification" id="notification">
        <span id="notificationText"></span>
    </div>

    <!-- Delete Confirmation Modal - Improved Design -->
    <div class="custom-modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">
                    <i class="bi bi-exclamation-triangle-fill"></i>Delete Meal Entry
                </h3>
            </div>
            <div class="modal-body">
                <p class="mb-3 text-center text-danger fw-bold">This action cannot be undone!</p>
                <p class="mb-3" id="deleteMessage">Are you sure you want to delete this meal entry?</p>

                <div class="delete-details" id="deleteDetails">
                    <!-- Details will be inserted here by JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex gap-2 w-100">
                    <button class="btn btn-danger flex-fill fw-bold" onclick="confirmDelete()">
                        <i class="bi bi-trash me-2"></i>Delete
                    </button>
                    <button class="btn btn-outline-secondary flex-fill" onclick="closeDeleteModal()">
                        <i class="bi bi-x-circle me-2"></i>Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="app-container">
        <!-- Header -->
        <div class="app-header">
            <div class="main-content">
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <h1 class="app-title">
                            <i class="bi bi-egg-fried"></i>MealMaster Pro
                        </h1>
                        <p class="app-subtitle">Smart Meal Management System</p>
                    </div>
                    <div class="month-indicator">
                        <i class="bi bi-calendar3 me-2"></i>
                        <span id="currentMonthDisplay">Select Month</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Control Panel -->
            <div class="control-panel">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-5 col-md-12">
                        <label class="form-label"><i class="bi bi-calendar-range me-2"></i>Select Month & Year</label>
                        <div class="row g-2">
                            <div class="col-7">
                                <select id="filter_month" class="form-select">
                                    <option value="">-- Select Month --</option>
                                    <option value="01">January</option>
                                    <option value="02">February</option>
                                    <option value="03">March</option>
                                    <option value="04">April</option>
                                    <option value="05">May</option>
                                    <option value="06">June</option>
                                    <option value="07">July</option>
                                    <option value="08">August</option>
                                    <option value="09">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="col-5">
                                <select id="filter_year" class="form-select">
                                    <option value="2024">2024</option>
                                    <option value="2025">2025</option>
                                    <option value="2026">2026</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <button class="btn btn-primary w-100 fw-bold" onclick="loadMonthData()" id="loadBtn">
                            <i class="bi bi-cloud-arrow-down me-2"></i>Load Month Data
                        </button>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <button class="btn btn-outline-primary w-100 fw-bold" onclick="showAddMealForm()">
                            <i class="bi bi-plus-circle me-2"></i>Add Meal
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="stats-grid">
                <div class="stats-card">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-people-fill me-2"></i>Total Members</h5>
                        <h2 id="totalMembers" class="card-text">0</h2>
                        <i class="bi bi-people-fill stats-icon"></i>
                    </div>
                </div>
                <div class="stats-card success">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-egg-fried me-2"></i>Total Meals</h5>
                        <h2 id="totalMealsCard" class="card-text">0</h2>
                        <i class="bi bi-egg-fried stats-icon"></i>
                    </div>
                </div>
                <div class="stats-card accent">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-calendar-check me-2"></i>Active Days</h5>
                        <h2 id="daysWithMeals" class="card-text">0</h2>
                        <i class="bi bi-calendar-check stats-icon"></i>
                    </div>
                </div>
                <div class="stats-card warning">
                    <div class="card-body position-relative">
                        <h5 class="card-title"><i class="bi bi-lightning-fill me-2"></i>Avg per Day</h5>
                        <h2 id="avgPerDay" class="card-text">0</h2>
                        <i class="bi bi-lightning-fill stats-icon"></i>
                    </div>
                </div>
            </div>

            <!-- Add Meal Form -->
            <div class="meal-form-container" id="mealFormContainer" style="display: none;">
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="bi bi-plus-circle"></i>Add New Meal Entry
                    </h3>
                    <form id="addMealForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-person me-2"></i>Select Member
                                </label>
                                <select id="memberSelect" class="form-select" required>
                                    <option value="">-- Select Member --</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-calendar me-2"></i>Date
                                </label>
                                <input type="date" id="mealDate" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-egg-fried me-2"></i>Number of Meals
                                    <small class="text-muted d-block mt-1">Enter any value ≥ 0 (e.g., 1, 2.5, 0.75,
                                        3.25)</small>
                                </label>
                                <input type="number" id="mealCount" class="form-control" min="0" step="any" value="1"
                                    placeholder="Enter meal count" required oninput="validateMealCount(this)">
                                <div class="form-text">
                                    <i class="bi bi-info-circle me-1"></i>
                                    You can enter any decimal value (0.1, 1.5, 2.75, etc.)
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <i class="bi bi-chat-text me-2"></i>Description (Optional)
                                </label>
                                <input type="text" id="mealDescription" class="form-control"
                                    placeholder="e.g., Lunch, Dinner, Breakfast, Special Meal">
                            </div>
                            <div class="col-12">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary flex-fill" id="saveBtn">
                                        <i class="bi bi-save me-2"></i>Save Meal
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary flex-fill"
                                        onclick="hideAddMealForm()">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Meal History -->
            <div class="data-container">
                <h3 class="form-section-title">
                    <i class="bi bi-clock-history"></i>Meal History for <span id="currentMonthTitle">Selected
                        Month</span>
                </h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Member</th>
                                <th>Meals</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="mealHistoryBody">
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox me-2"></i>Select a month and click "Load Month Data"
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Member Totals -->
            <div class="data-container">
                <h3 class="form-section-title">
                    <i class="bi bi-bar-chart"></i>Member Totals for <span id="currentMonthTotalsTitle">Selected
                        Month</span>
                </h3>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Member Name</th>
                                <th>Total Meals</th>
                                <th>Summary</th>
                            </tr>
                        </thead>
                        <tbody id="memberTotalsBody">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox me-2"></i>No data available
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <!-- Floating Action Button -->
    <button class="floating-btn" onclick="showAddMealForm()" title="Add New Meal">
        <i class="bi bi-plus-lg"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Global variables
        let members = [];
        let currentMonth = '';
        let currentYear = new Date().getFullYear().toString();
        let currentMeals = [];
        let mealToDelete = null;

        // Initialize the application
        document.addEventListener('DOMContentLoaded', function () {
            console.log('🚀 MealMaster Pro Initialized');
            initializeApp();
        });

        async function initializeApp() {
            await loadMembers();
            setupEventListeners();
            setDefaultDate();
            setupQuickInputShortcuts(); // Add this line

            // Auto-load current month data
            setTimeout(() => {
                if (currentMonth && currentYear) {
                    loadMonthData();
                }
            }, 500);
        }

        function setupEventListeners() {
            // Form submission
            document.getElementById('addMealForm').addEventListener('submit', function (e) {
                e.preventDefault();
                saveMeal();
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', function (e) {
                if (e.ctrlKey && e.key === 'n') {
                    e.preventDefault();
                    showAddMealForm();
                }
                if (e.key === 'Escape') {
                    closeDeleteModal();
                    hideAddMealForm();
                }
            });
        }

        function setDefaultDate() {
            const now = new Date();
            const month = (now.getMonth() + 1).toString().padStart(2, '0');
            const year = now.getFullYear().toString();

            document.getElementById('filter_month').value = month;
            document.getElementById('filter_year').value = year;

            // Set today's date as default for new meals
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('mealDate').value = today;

            currentMonth = month;
            currentYear = year;
            updateMonthDisplay(month, year);
        }

        /* LOAD MEMBERS */
        async function loadMembers() {
            try {
                console.log('Loading members...');
                const response = await fetch("process/meal_actions.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({ action: "load_members" })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Members response:', data);

                if (data.status === 'success') {
                    members = data.members || data.data?.members || [];
                    updateMemberCount();
                    populateMemberSelect();
                    console.log(`✅ Loaded ${members.length} members`);
                    showNotification('Members loaded successfully', 'success');
                } else {
                    throw new Error(data.message || 'Failed to load members');
                }
            } catch (error) {
                console.error('❌ Error loading members:', error);
                showNotification('Failed to load members: ' + error.message, 'error');
                members = [];
            }
        }

        function updateMemberCount() {
            const totalMembersElement = document.getElementById('totalMembers');
            if (totalMembersElement) {
                totalMembersElement.textContent = members.length;
            }
        }

        function populateMemberSelect() {
            const memberSelect = document.getElementById('memberSelect');
            memberSelect.innerHTML = '<option value="">-- Select Member --</option>';

            members.forEach(member => {
                const option = document.createElement('option');
                option.value = member.id;
                option.textContent = member.name; // Removed email as requested
                memberSelect.appendChild(option);
            });
        }

        /* LOAD MONTH DATA - FIXED */
        async function loadMonthData() {
            const month = document.getElementById("filter_month").value;
            const year = document.getElementById("filter_year").value;

            if (!month) {
                showNotification('Please select a month first!', 'warning');
                return;
            }

            currentMonth = month;
            currentYear = year;

            try {
                showButtonLoading('loadBtn', 'Loading...');
                updateMonthDisplay(month, year);

                console.log(`📅 Loading meals for ${year}-${month}`);
                const response = await fetch("process/meal_actions.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: "load_meals_month",
                        month: month,
                        year: year
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();
                console.log('Month data response:', data);

                if (data.status === 'success') {
                    // Handle different response structures
                    currentMeals = data.meals || data.data?.meals || [];

                    // Handle member totals
                    let memberTotals = {};
                    if (data.member_totals) {
                        memberTotals = data.member_totals;
                    } else if (data.data?.member_totals) {
                        memberTotals = data.data.member_totals;
                    } else if (data.monthly_totals || data.data?.monthly_totals) {
                        const monthlyTotals = data.monthly_totals || data.data?.monthly_totals || [];
                        monthlyTotals.forEach(item => {
                            memberTotals[item.member_id] = item;
                        });
                    }

                    const statistics = data.statistics || data.data?.statistics || {};

                    console.log('Processed data:', {
                        meals: currentMeals.length,
                        memberTotals: Object.keys(memberTotals).length,
                        statistics: statistics
                    });

                    displayMealHistory(currentMeals);
                    displayMemberTotals(memberTotals);
                    updateStatistics(statistics);

                    console.log(`✅ Loaded ${currentMeals.length} meals`);
                    showNotification(`Loaded data for ${getMonthName(month)} ${year}`, 'success');
                } else {
                    throw new Error(data.message || 'Failed to load meal data');
                }
            } catch (error) {
                console.error('❌ Error loading meal data:', error);
                showNotification('Failed to load meal data: ' + error.message, 'error');

                // Clear tables on error
                document.getElementById('mealHistoryBody').innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>Failed to load data
                    </td>
                </tr>
            `;

                document.getElementById('memberTotalsBody').innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-exclamation-triangle me-2"></i>Failed to load data
                    </td>
                </tr>
            `;

                // Reset statistics
                resetStatistics();
            } finally {
                hideButtonLoading('loadBtn', '<i class="bi bi-cloud-arrow-down me-2"></i>Load Month Data');
            }
        }

        function displayMealHistory(meals) {
            const tbody = document.getElementById('mealHistoryBody');

            if (!meals || meals.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-inbox me-2"></i>No meals found for selected month
                    </td>
                </tr>
            `;
                return;
            }

            tbody.innerHTML = meals.map(meal => `
            <tr>
                <td><strong>${formatDate(meal.meal_date)}</strong></td>
                <td>
                    <div class="fw-bold">${meal.member_name || meal.real_name || `Member ${meal.member_id}`}</div>
                </td>
                <td><span class="badge bg-primary fs-6">${formatMealValue(meal.meal_count)}</span></td>
                <td>${meal.description ? meal.description : '<span class="text-muted">-</span>'}</td>
                <td>
                    <div class="d-flex gap-1">
                        <button class="btn btn-edit action-btn" onclick="editMeal(${meal.id})" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-delete action-btn" onclick="showDeleteModal(${meal.id})" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
        }

        function displayMemberTotals(memberTotals) {
            const tbody = document.getElementById('memberTotalsBody');

            console.log('Displaying member totals:', memberTotals);

            if (!memberTotals || Object.keys(memberTotals).length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-inbox me-2"></i>No member totals available
                    </td>
                </tr>
            `;
                return;
            }

            // Create an array of all members with their totals
            const totalsArray = members.map(member => {
                const memberData = memberTotals[member.id];

                if (memberData && typeof memberData === 'object') {
                    return {
                        member_id: member.id,
                        member_name: memberData.member_name || memberData.name || member.name,
                        total_meals: parseFloat(memberData.total_meals) || 0
                    };
                } else {
                    return {
                        member_id: member.id,
                        member_name: member.name,
                        total_meals: parseFloat(memberData) || 0
                    };
                }
            }).filter(member => member.total_meals > 0)
                .sort((a, b) => b.total_meals - a.total_meals);

            console.log('Processed totals array:', totalsArray);

            if (totalsArray.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-inbox me-2"></i>No meals recorded for any member this month
                    </td>
                </tr>
            `;
                return;
            }

            tbody.innerHTML = totalsArray.map(member => `
            <tr>
                <td class="fw-bold">${member.member_name}</td>
                <td><span class="badge bg-success fs-6">${formatMealValue(member.total_meals)}</span></td>
                <td>${formatMealValue(member.total_meals)} meals</td>
            </tr>
        `).join('');
        }

        /* MEAL COUNT VALIDATION FUNCTIONS */
        function validateMealCount(input) {
            const value = parseFloat(input.value);

            if (isNaN(value)) {
                input.setCustomValidity('Please enter a valid number');
                showInputError(input, 'Please enter a valid number');
                return false;
            }

            if (value < 0) {
                input.setCustomValidity('Meal count cannot be negative');
                showInputError(input, 'Meal count cannot be negative');
                return false;
            }

            // Clear any previous validation
            input.setCustomValidity('');
            clearInputError(input);
            return true;
        }

        function showInputError(input, message) {
            // Remove any existing error styling
            clearInputError(input);

            // Add error styling
            input.style.borderColor = 'var(--danger)';
            input.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.1)';

            // Create or update error message
            let errorElement = input.parentNode.querySelector('.input-error-message');
            if (!errorElement) {
                errorElement = document.createElement('div');
                errorElement.className = 'input-error-message text-danger small mt-1';
                input.parentNode.appendChild(errorElement);
            }
            errorElement.innerHTML = `<i class="bi bi-exclamation-triangle me-1"></i>${message}`;
        }

        function clearInputError(input) {
            input.style.borderColor = '';
            input.style.boxShadow = '';

            const errorElement = input.parentNode.querySelector('.input-error-message');
            if (errorElement) {
                errorElement.remove();
            }
        }

        /* ENHANCED SAVE MEAL FUNCTION */
        async function saveMeal() {
            const memberId = document.getElementById('memberSelect').value;
            const mealDate = document.getElementById('mealDate').value;
            const mealCountInput = document.getElementById('mealCount');
            const mealCount = mealCountInput.value;
            const description = document.getElementById('mealDescription').value;

            // Validate all required fields
            if (!memberId || !mealDate || !mealCount) {
                showNotification('Please fill all required fields', 'warning');
                return;
            }

            // Validate meal count with enhanced validation
            if (!validateMealCount(mealCountInput)) {
                return;
            }

            const mealCountNum = parseFloat(mealCount);

            // Additional validation
            if (isNaN(mealCountNum)) {
                showInputError(mealCountInput, 'Please enter a valid number');
                showNotification('Please enter a valid meal count', 'warning');
                return;
            }

            if (mealCountNum < 0) {
                showInputError(mealCountInput, 'Meal count cannot be negative');
                showNotification('Meal count cannot be negative', 'warning');
                return;
            }

            try {
                showButtonLoading('saveBtn', 'Saving...');

                const formData = {
                    member_id: memberId,
                    meal_count: mealCountNum,
                    meal_date: mealDate,
                    description: description
                };

                console.log('Saving meal:', formData);

                const response = await fetch("process/meal_actions.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: "add_meal",
                        ...formData
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Save response:', result);

                if (result.status === 'success') {
                    showNotification('Meal saved successfully!', 'success');
                    resetMealForm();
                    hideAddMealForm();

                    // Reload the current month data if we're viewing a month
                    if (currentMonth) {
                        setTimeout(() => {
                            loadMonthData();
                        }, 500);
                    }
                } else {
                    throw new Error(result.message || 'Failed to save meal');
                }
            } catch (error) {
                console.error('❌ Error saving meal:', error);
                showNotification('Failed to save meal: ' + error.message, 'error');
            } finally {
                hideButtonLoading('saveBtn', '<i class="bi bi-save me-2"></i>Save Meal');
            }
        }

        /* DELETE MEAL FUNCTIONALITY - FIXED */
        function showDeleteModal(mealId) {
            const meal = currentMeals.find(m => m.id == mealId);
            if (meal) {
                mealToDelete = mealId;

                // Create detailed delete information
                const deleteDetails = `
                <div class="delete-detail-item">
                    <span class="delete-detail-label">Member:</span>
                    <span class="delete-detail-value">${meal.member_name || meal.real_name || `Member ${meal.member_id}`}</span>
                </div>
                <div class="delete-detail-item">
                    <span class="delete-detail-label">Date:</span>
                    <span class="delete-detail-value">${formatDate(meal.meal_date)}</span>
                </div>
                <div class="delete-detail-item">
                    <span class="delete-detail-label">Meals:</span>
                    <span class="delete-detail-value">${formatMealValue(meal.meal_count)}</span>
                </div>
                <div class="delete-detail-item">
                    <span class="delete-detail-label">Description:</span>
                    <span class="delete-detail-value">${meal.description || 'None'}</span>
                </div>
            `;

                document.getElementById('deleteDetails').innerHTML = deleteDetails;
                document.getElementById('deleteModal').style.display = 'block';

                // Prevent body scroll when modal is open
                document.body.style.overflow = 'hidden';
            } else {
                showNotification('Meal not found for deletion', 'error');
            }
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            mealToDelete = null;
            // Restore body scroll
            document.body.style.overflow = '';
        }

        async function confirmDelete() {
            if (!mealToDelete) {
                showNotification('No meal selected for deletion', 'error');
                return;
            }

            try {
                showNotification('Deleting meal...', 'info');

                const response = await fetch("process/meal_actions.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: "delete_meal",
                        id: mealToDelete
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Delete response:', result);

                if (result.status === 'success') {
                    showNotification('Meal deleted successfully!', 'success');
                    closeDeleteModal();

                    // Reload current data
                    if (currentMonth) {
                        setTimeout(() => {
                            loadMonthData();
                        }, 500);
                    }
                } else {
                    throw new Error(result.message || 'Failed to delete meal');
                }
            } catch (error) {
                console.error('❌ Error deleting meal:', error);
                showNotification('Failed to delete meal: ' + error.message, 'error');
            } finally {
                mealToDelete = null;
            }
        }

        /* FORM MANAGEMENT */
        function showAddMealForm() {
            document.getElementById('mealFormContainer').style.display = 'block';
            // Scroll to form
            document.getElementById('mealFormContainer').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        function hideAddMealForm() {
            document.getElementById('mealFormContainer').style.display = 'none';
            resetMealForm();
        }

        function resetMealForm() {
            document.getElementById('addMealForm').reset();
            document.getElementById('mealCount').value = '1';
            // Set today's date
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('mealDate').value = today;
        }

        /* ENHANCED EDIT MEAL FUNCTION */
        function editMeal(mealId) {
            const meal = currentMeals.find(m => m.id == mealId);
            if (meal) {
                // Populate form with meal data
                document.getElementById('memberSelect').value = meal.member_id;
                document.getElementById('mealDate').value = meal.meal_date;
                document.getElementById('mealCount').value = meal.meal_count;
                document.getElementById('mealDescription').value = meal.description || '';

                // Show the form
                showAddMealForm();

                showNotification('Edit mode activated. Update the values and save.', 'info');

                // Focus on the meal count input for easy editing
                setTimeout(() => {
                    document.getElementById('mealCount').focus();
                    document.getElementById('mealCount').select();
                }, 300);
            }
        }

        /* QUICK INPUT SHORTCUTS */
        function setupQuickInputShortcuts() {
            const mealCountInput = document.getElementById('mealCount');

            if (!mealCountInput) return;

            // Add event listener for common quick inputs
            mealCountInput.addEventListener('keydown', function (e) {
                // Quick shortcuts for common values
                if (e.ctrlKey) {
                    switch (e.key) {
                        case '1':
                            e.preventDefault();
                            this.value = '1';
                            break;
                        case '2':
                            e.preventDefault();
                            this.value = '2';
                            break;
                        case '5':
                            e.preventDefault();
                            this.value = '0.5';
                            break;
                        case '0':
                            e.preventDefault();
                            this.value = '0';
                            break;
                    }
                }
            });

            // Add input for decimal values
            mealCountInput.addEventListener('input', function (e) {
                // Allow any decimal input but ensure it's valid
                const value = this.value;
                if (value === '' || value === '.') {
                    return; // Allow empty or just decimal point for user convenience
                }

                const numValue = parseFloat(value);
                if (!isNaN(numValue) && numValue >= 0) {
                    clearInputError(this);
                }
            });

            // Real-time validation as user types
            mealCountInput.addEventListener('blur', function () {
                validateMealCount(this);
            });
        }

        /* HELPER FUNCTIONS */
        function formatDate(dateString) {
            try {
                const date = new Date(dateString);
                return date.toLocaleDateString('en-US', {
                    weekday: 'short',
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
            } catch (e) {
                return dateString;
            }
        }

        function formatMealValue(value) {
            if (value === 0 || value === '0') return '0';
            const num = parseFloat(value);
            if (isNaN(num)) return '0';
            if (Number.isInteger(num)) return num.toString();
            return num.toFixed(1);
        }

        function updateStatistics(statistics) {
            if (!statistics) {
                resetStatistics();
                return;
            }

            const elements = {
                'totalMealsCard': formatMealValue(statistics.total_meals || 0),
                'daysWithMeals': statistics.unique_days || statistics.days_with_meals || 0,
                'avgPerDay': statistics.avg_per_day || '0'
            };

            Object.entries(elements).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = value;
                }
            });
        }

        function resetStatistics() {
            document.getElementById('totalMealsCard').textContent = '0';
            document.getElementById('daysWithMeals').textContent = '0';
            document.getElementById('avgPerDay').textContent = '0';
        }

        function updateMonthDisplay(month, year) {
            const monthName = getMonthName(month);
            const monthDisplay = document.getElementById('currentMonthDisplay');
            const monthTitle = document.getElementById('currentMonthTitle');
            const monthTotalsTitle = document.getElementById('currentMonthTotalsTitle');

            if (monthDisplay) monthDisplay.textContent = `${monthName} ${year}`;
            if (monthTitle) monthTitle.textContent = `${monthName} ${year}`;
            if (monthTotalsTitle) monthTotalsTitle.textContent = `${monthName} ${year}`;
        }

        function getMonthName(month) {
            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"];
            return monthNames[parseInt(month) - 1] || 'Unknown Month';
        }

        /* UI UTILITIES - FIXED */
        function showButtonLoading(buttonId, text) {
            const btn = document.getElementById(buttonId);
            if (btn) {
                btn.innerHTML = `<span class="loading-spinner me-2"></span>${text}`;
                btn.disabled = true;
            }
        }

        function hideButtonLoading(buttonId, text) {
            const btn = document.getElementById(buttonId);
            if (btn) {
                btn.innerHTML = text;
                btn.disabled = false;
            }
        }

        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const notificationText = document.getElementById('notificationText');

            if (notification && notificationText) {
                notificationText.textContent = message;
                notification.className = 'notification';

                if (type === 'error') {
                    notification.classList.add('error');
                } else if (type === 'warning') {
                    notification.classList.add('warning');
                }

                notification.style.display = 'block';

                // Auto hide after 3 seconds
                setTimeout(() => {
                    notification.style.display = 'none';
                }, 3000);
            }
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Escape key to close modal
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>

</html>