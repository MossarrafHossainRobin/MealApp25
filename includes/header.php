<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MealApp25</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
        }

        .hover-effect:hover {
            background-color: #d1fae5 !important;
            color: #059669 !important;
            transition: 0.3s;
        }

        .navbar-custom {
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            font-weight: 600;
        }

        .navbar-custom .nav-link {
            color: #16a34a;
            margin-right: 15px;
            transition: 0.3s;
            font-weight: 500;
        }

        .navbar-custom .nav-link:hover {
            color: #059669;
            background: #d1fae5;
            border-radius: 8px;
            padding: 5px 10px;
        }

        .section {
            display: none;
            padding: 30px;
        }

        .section.active {
            display: block;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .chart-container {
            max-width: 600px;
            margin: auto;
        }

        .meal-input {
            width: 60px;
            padding: 5px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .owes {
            color: #dc3545;
            font-weight: bold;
        }

        .gets {
            color: #198754;
            font-weight: bold;
        }

        .water-check {
            width: 26px;
            height: 26px;
            border-radius: 50%;
            border: 2px solid #198754;
            appearance: none;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            position: relative;
        }

        .water-check:checked {
            background-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.25);
        }

        .water-check:checked::after {
            content: '✔';
            color: #fff;
            position: absolute;
            top: 0;
            left: 6px;
            font-size: 14px;
            font-weight: bold;
        }

        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
        }

        .nav-link.active {
            background: #d1fae5 !important;
            color: #059669 !important;
            border-radius: 8px;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            display: none;

            /* Enhanced Responsive Design */
            .section {
                display: none;
                padding: 20px 15px;
                min-height: calc(100vh - 80px);
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            }

            .section.active {
                display: block;
                animation: fadeInUp 0.4s ease-out;
            }

            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Card Enhancements */
            .enhanced-card {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                border-radius: 20px;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1), 0 2px 8px rgba(0, 0, 0, 0.05);
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                margin-bottom: 1.5rem;
                overflow: hidden;
            }

            .enhanced-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15), 0 4px 16px rgba(0, 0, 0, 0.1);
            }

            .enhanced-card-header {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
                border-radius: 20px 20px 0 0 !important;
                padding: 1.25rem 1.5rem;
                border: none;
            }

            .enhanced-card-body {
                padding: 1.5rem;
            }

            /* Responsive Tables */
            .responsive-table {
                border-radius: 12px;
                overflow: hidden;
                background: white;
            }

            .table-container {
                overflow-x: auto;
                border-radius: 12px;
                background: white;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                -webkit-overflow-scrolling: touch;
            }

            /* Mobile First Grid */
            .mobile-grid {
                display: grid;
                gap: 1rem;
                grid-template-columns: 1fr;
            }

            @media (min-width: 768px) {
                .mobile-grid {
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 1.5rem;
                }
            }

            /* Stats Cards */
            .stat-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                border-radius: 16px;
                padding: 1.5rem;
                text-align: center;
                transition: all 0.3s ease;
                margin-bottom: 1rem;
            }

            .stat-card:hover {
                transform: scale(1.02);
            }

            .stat-number {
                font-size: 2rem;
                font-weight: 700;
                margin-bottom: 0.5rem;
                line-height: 1;
            }

            .stat-label {
                font-size: 0.9rem;
                opacity: 0.9;
                font-weight: 500;
            }

            /* Button Enhancements */
            .btn-app {
                border-radius: 12px;
                padding: 0.75rem 1.5rem;
                font-weight: 600;
                border: none;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .btn-app:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            }

            .btn-app-primary {
                background: linear-gradient(135deg, #10b981 0%, #059669 100%);
                color: white;
            }

            .btn-app-secondary {
                background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                color: white;
            }

            /* Form Controls */
            .form-control-app {
                border-radius: 12px;
                border: 2px solid #e2e8f0;
                padding: 0.75rem 1rem;
                font-size: 1rem;
                transition: all 0.3s ease;
            }

            .form-control-app:focus {
                border-color: #10b981;
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            }

            /* Meal Input Enhancements */
            .meal-input-app {
                width: 60px;
                padding: 0.5rem;
                text-align: center;
                border: 2px solid #e2e8f0;
                border-radius: 10px;
                font-weight: 600;
                transition: all 0.3s ease;
                font-size: 0.9rem;
            }

            .meal-input-app:focus {
                border-color: #10b981;
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
                transform: scale(1.05);
            }

            /* Mobile Optimizations */
            @media (max-width: 768px) {
                .navbar-nav {
                    flex-direction: row;
                    overflow-x: auto;
                    padding-bottom: 0.5rem;
                    -webkit-overflow-scrolling: touch;
                    flex-wrap: nowrap;
                }

                .nav-link {
                    white-space: nowrap;
                    font-size: 0.8rem;
                    padding: 0.5rem;
                    margin-right: 0.5rem;
                }

                .section {
                    padding: 15px 10px;
                }

                .enhanced-card-body {
                    padding: 1rem;
                }

                .stat-number {
                    font-size: 1.5rem;
                }

                .stat-card {
                    padding: 1rem;
                }

                .btn-app {
                    padding: 0.6rem 1.2rem;
                    font-size: 0.9rem;
                }

                h2 {
                    font-size: 1.5rem;
                }
            }

            /* Transaction Cards */
            .transaction-card {
                background: white;
                border-radius: 12px;
                padding: 1rem;
                margin-bottom: 0.75rem;
                border-left: 4px solid #10b981;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
            }

            .transaction-card:hover {
                transform: translateX(5px);
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            }

            /* Enhanced Water Check */
            .water-check-app {
                width: 24px;
                height: 24px;
                border-radius: 50%;
                border: 2px solid #10b981;
                appearance: none;
                cursor: pointer;
                transition: all 0.3s ease;
                position: relative;
                background: white;
                flex-shrink: 0;
            }

            .water-check-app:checked {
                background: #10b981;
                box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.3);
            }

            .water-check-app:checked::after {
                content: '✓';
                color: white;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                font-size: 12px;
                font-weight: bold;
            }

            /* Balance Indicators */
            .balance-positive {
                background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 10px;
                font-weight: 600;
            }

            .balance-negative {
                background: linear-gradient(135deg, #ef4444 0%, #f87171 100%);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 10px;
                font-weight: 600;
            }

            .balance-neutral {
                background: #6b7280;
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 10px;
                font-weight: 600;
            }

            /* Scrollbar Styling */
            .table-container::-webkit-scrollbar {
                height: 6px;
            }

            .table-container::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }

            .table-container::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 10px;
            }

            .table-container::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            /* Icon Sizes */
            .icon-lg {
                font-size: 1.5rem;
            }

            .icon-xl {
                font-size: 2rem;
            }

            /* Loading Animation */
            @keyframes pulse {
                0% {
                    opacity: 1;
                }

                50% {
                    opacity: 0.5;
                }

                100% {
                    opacity: 1;
                }
            }

            .loading-pulse {
                animation: pulse 2s infinite;
            }

            /* Badge Styles */
            .badge-app {
                border-radius: 20px;
                padding: 0.4rem 0.8rem;
                font-weight: 600;
                font-size: 0.75rem;
            }

            /* Mobile Table Responsive */
            @media (max-width: 576px) {
                .table-responsive-sm {
                    font-size: 0.8rem;
                }

                .meal-input-app {
                    width: 50px;
                    padding: 0.4rem;
                    font-size: 0.8rem;
                }

                .enhanced-card-header {
                    padding: 1rem;
                }

                .enhanced-card-header h5 {
                    font-size: 1rem;
                }
            }
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
        }
    </style>
</head>

<body></body>
</body>