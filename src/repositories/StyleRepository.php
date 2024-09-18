<?php 
namespace Repositories;

class StyleRepository {
    public function homeStyle() {
        return '<style>
                    h1,h2,h3 {
                        text-align: center;
                    }

                    body {
                        background-image: url("../../assets/image/background.jpg");
                        padding-top: 48px;
                    }

                    .mt-4 {
                        max-height: 500px;
                        overflow-y: auto;
                    }

                    .breadcrumb-custom {
                        background: linear-gradient(to right, #ffffff, #ccedb6) !important;
                        border-radius: 5px !important;
                        padding: 10px !important;
                        margin-bottom: 20px !important;
                        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1) !important;
                    }

                    .breadcrumb-custom .breadcrumb-item a {
                        color: #006400 !important;
                    }

                    .breadcrumb-custom .breadcrumb-item.active {
                        color: #333333 !important;
                        font-weight: bold !important;
                    }
                    .img-fluid {
                        max-width: 100%;
                        height: auto;
                        border-radius: 15px;
                        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
                        transition: transform 0.2s ease-in-out;
                    }

                    .img-fluid:hover {
                        transform: scale(1.05);
                    }

                    .containerr {
                        padding: 20px;
                        background-color: #ffffff;
                        border-radius: 15px;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }

                    h1 {
                        color: #006400;
                    }

                    p {
                        font-size: 1.1em;
                        line-height: 1.6;
                    }
                    </style>';
    }
    public function loginStyle() {
        return '<style>
                        body {
                            background-image: url("../../assets/image/background.jpg");
                            background-size: cover;
                            padding-top: 68px; /* Un padding pour régler le décalage à cause de la class fixed-top de la navbar */
                            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
                        }

                        .custom {
                            background: rgba(255, 255, 255, 0.85);
                            border-radius: 15px;
                            padding: 30px;
                            margin-top: 50px;
                            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
                        }

                        h1, h2, h3 {
                            text-align: center;
                            color: #006400;
                            margin-bottom: 20px;
                        }

                        .form-group label {
                            font-weight: bold;
                        }

                        .input-group .form-control, .input-group-append .btn {
                            border-radius: 0;
                        }

                        .alert {
                            text-align: center;
                            font-weight: bold;
                        }

                        .btn-success, .btn-outline-danger {
                            width: 100%;
                            margin-top: 10px;
                        }

                        .modal-content {
                            border-radius: 15px;
                        }

                        #togglePassword {
                            border: none;
                            background: transparent;
                            color: #007bff;
                        }

                        #togglePassword:hover {
                            color: #0056b3;
                        }
                        </style>';
    }
    public function manageServiceStyle() {
        return '
            <style>
h1, h2, h3 {
    text-align: center;
}

body {
    background-image: url("../../../../assets/image/background.jpg");
}

.mt-4 {
    background: whitesmoke;
    border-radius: 15px;
}

.card {
    margin-bottom: 20px;
    transition: transform 0.3s;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.card:hover {
    transform: scale(1.05);
}

.card-img-top {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

.button-row {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}
</style>
        ';
    }

    public function manageHabitatStyle() 
    {
        return '
            <style>
                h1, h2, h3 {
                    text-align: center;
                }
                body {
                    background-image: url("../../../../assets/image/background.jpg");
                }
                .mt-4 {
                    background: whitesmoke;
                    border-radius: 15px;
                }
                .card {
                    margin-bottom: 20px;
                    transition: transform 0.3s;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .card:hover {
                    transform: scale(1.05);
                }

                .card-img-top {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                }
                </style>
                        ';
    }
    public function manageReportsStyle() 
    {
        return '
            <style>
                h1, h2, h3 {
                    text-align: center;
                }
                body {
                    background-image: url("../../../../assets/image/background.jpg");
                }
                .mt-4 {
                    background: whitesmoke;
                    border-radius: 15px;
                }
                .report-card {
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    padding: 20px;
                    background-color: #fff;
                    margin-bottom: 20px;
                }
                .card-title {
                    color: #e67e22;
                    font-weight: bold;
                    margin-bottom: 20px;
                }
                .card-text {
                    font-size: 1.1em;
                    margin-bottom: 10px;
                }
                </style>
                        ';
    }
}