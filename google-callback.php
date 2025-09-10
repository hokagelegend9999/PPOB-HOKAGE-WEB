<?php
session_start();

// Pastikan semua require file tersedia
if (!file_exists('vendor/autoload.php') || 
    !file_exists('config.php') || 
    !file_exists('db_connect.php')) {
    error_log("Required files are missing");
    header('Location: login_form.php?error=server_config');
    exit();
}

require_once 'vendor/autoload.php';
require_once 'config.php';
require_once 'db_connect.php';

// Validasi apakah konstanta sudah didefinisikan
if (!defined('GOOGLE_CLIENT_ID') || 
    !defined('GOOGLE_CLIENT_SECRET') || 
    !defined('GOOGLE_REDIRECT_URL')) {
    error_log("Google OAuth constants not defined");
    header('Location: login_form.php?error=server_config');
    exit();
}

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URL);
$client->addScope("email");
$client->addScope("profile");

if (isset($_GET['code'])) {
    // Validasi code parameter
    $code = trim($_GET['code']);
    if (empty($code)) {
        header('Location: login_form.php?error=invalid_auth_code');
        exit();
    }
    
    try {
        $token = $client->fetchAccessTokenWithAuthCode($code);
        
        if (isset($token['error'])) {
            error_log("Google Auth Error: " . $token['error']);
            header('Location: login_form.php?error=google_auth_failed');
            exit();
        }

        if (!isset($token['access_token'])) {
            error_log("Access token not received from Google");
            header('Location: login_form.php?error=google_auth_failed');
            exit();
        }
        
        $client->setAccessToken($token['access_token']);
        
        // Dapatkan data profil pengguna dari Google
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        // Validasi data yang diterima dari Google
        if (empty($google_account_info->id) || empty($google_account_info->email)) {
            error_log("Incomplete user data from Google");
            header('Location: login_form.php?error=google_incomplete_data');
            exit();
        }
        
        $google_id = $google_account_info->id;
        $email = filter_var($google_account_info->email, FILTER_SANITIZE_EMAIL);
        $name = !empty($google_account_info->name) ? 
                filter_var($google_account_info->name, FILTER_SANITIZE_STRING) : 
                explode('@', $email)[0];
        $profile_picture_url = !empty($google_account_info->picture) ? 
                              filter_var($google_account_info->picture, FILTER_SANITIZE_URL) : 
                              '';

        // Cek apakah user dengan google_id ini sudah ada
        $stmt = $pdo->prepare("SELECT * FROM users WHERE google_id = ?");
        $stmt->execute([$google_id]);
        $user = $stmt->fetch();

        if ($user) {
            // Jika user sudah ada, langsung login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['auth_provider'] = 'google';
            
            // Regenerate session ID untuk keamanan
            session_regenerate_id(true);
            
            header('Location: index.php');
            exit();
        } else {
            // Jika user belum ada (pendaftaran baru via Google)
            
            // Cek dulu apakah emailnya sudah terdaftar secara manual
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND auth_provider = 'local'");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                header('Location: login_form.php?error=email_exists_manual');
                exit();
            }

            $new_profile_picture_name = 'default_avatar.png';
            
            // Unduh foto profil hanya jika URL valid
            if (!empty($profile_picture_url) && filter_var($profile_picture_url, FILTER_VALIDATE_URL)) {
                $image_data = @file_get_contents($profile_picture_url);
                
                if ($image_data !== false) {
                    // Buat direktori jika belum ada
                    if (!file_exists('uploads/avatars')) {
                        mkdir('uploads/avatars', 0755, true);
                    }
                    
                    $new_profile_picture_name = 'user_' . $google_id . '_' . time() . '.jpg';
                    $path_to_save = 'uploads/avatars/' . $new_profile_picture_name;
                    
                    if (file_put_contents($path_to_save, $image_data) === false) {
                        error_log("Failed to save profile picture");
                        // Gunakan avatar default jika gagal menyimpan
                        $new_profile_picture_name = 'default_avatar.png';
                    }
                }
            }

            // Buat user baru di database
            $stmt = $pdo->prepare(
                "INSERT INTO users (username, email, password, google_id, auth_provider, profile_picture, role) 
                 VALUES (?, ?, NULL, ?, 'google', ?, 'user')"
            );
            
            if ($stmt->execute([$name, $email, $google_id, $new_profile_picture_name])) {
                $new_user_id = $pdo->lastInsertId();

                // Login user baru
                $_SESSION['user_id'] = $new_user_id;
                $_SESSION['username'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'user';
                $_SESSION['auth_provider'] = 'google';
                
                // Regenerate session ID untuk keamanan
                session_regenerate_id(true);
                
                header('Location: index.php');
                exit();
            } else {
                error_log("Failed to insert new user into database");
                header('Location: login_form.php?error=registration_failed');
                exit();
            }
        }
    } catch (Google_Service_Exception $e) {
        error_log("Google Service Exception: " . $e->getMessage());
        header('Location: login_form.php?error=google_service_error');
        exit();
    } catch (Google_Exception $e) {
        error_log("Google Client Exception: " . $e->getMessage());
        header('Location: login_form.php?error=google_client_error');
        exit();
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        header('Location: login_form.php?error=database_error');
        exit();
    } catch (Exception $e) {
        error_log("Unexpected error: " . $e->getMessage());
        header('Location: login_form.php?error=unexpected_error');
        exit();
    }
} else {
    // Jika tidak ada kode, kembali ke login
    header('Location: login_form.php');
    exit();
}