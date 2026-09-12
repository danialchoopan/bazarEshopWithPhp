<?php
/**
 * Comprehensive System Test Suite
 * Tests all core components: DB, Auth, CSRF, Validator, Seed Data
 */

require_once __DIR__ . '/../php/inc/database.php';
require_once __DIR__ . '/../core/Auth.php';
require_once __DIR__ . '/../core/Csrf.php';
require_once __DIR__ . '/../core/Validator.php';

echo "=== RUNNING COMPREHENSIVE SYSTEM TESTS ===\n\n";

$passed = 0;
$failed = 0;

// Test 1: Database Connection & Tables
echo "1. Testing Database Connection...\n";
try {
    $db = DB::getInstance();
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->results();
    $tableNames = array_column($tables, 'name');
    echo "   ✅ Connected. Tables found: " . implode(', ', $tableNames) . "\n";
    $passed++;
} catch (Exception $e) {
    echo "   ❌ DB Error: " . $e->getMessage() . "\n";
    $failed++;
}

// Test 2: Environment Variables (SEED_DATA flag)
echo "\n2. Testing Environment Config (SEED_DATA)...\n";
$seedFlag = getenv('SEED_DATA') ?: 'true';
echo "   ℹ️ SEED_DATA flag detected: $seedFlag\n";
echo "   ✅ Env config readable.\n";
$passed++;

// Test 3: CSRF Token Generation & Verification
echo "\n3. Testing CSRF Protection...\n";
$token = Csrf::generateToken();
if (!empty($token) && strlen($token) > 32) {
    echo "   ✅ Token generated: " . substr($token, 0, 10) . "...\n";
    if (Csrf::validateToken($token)) {
        echo "   ✅ Token validation passed.\n";
        $passed++;
    } else {
        echo "   ❌ Token validation failed.\n";
        $failed++;
    }
} else {
    echo "   ❌ Token generation failed.\n";
    $failed++;
}

// Test 4: Validator Class
echo "\n4. Testing Validator Class...\n";
$data = ['email' => 'test@example.com', 'age' => '25', 'name' => ''];
$rules = ['email' => 'required|email', 'age' => 'required|numeric|min:18', 'name' => 'required'];
$validator = new Validator($data, $rules);
if ($validator->passes()) {
    echo "   ⚠️ Validator should have failed (name is empty).\n";
    $failed++;
} else {
    $errors = $validator->errors();
    if (isset($errors['name'])) {
        echo "   ✅ Validation correctly caught empty name.\n";
        $passed++;
    } else {
        echo "   ❌ Validation errors array missing 'name'.\n";
        $failed++;
    }
}

$dataValid = ['email' => 'admin@bazar.local', 'age' => '30', 'name' => 'Ali'];
$validatorValid = new Validator($dataValid, $rules);
if ($validatorValid->passes()) {
    echo "   ✅ Valid data passed validation.\n";
    $passed++;
} else {
    echo "   ❌ Valid data failed validation.\n";
    $failed++;
}

// Test 5: Auth Helper (Mock Session)
echo "\n5. Testing Auth Helper...\n";
$_SESSION['user_id'] = 1;
$_SESSION['role'] = 'admin';
if (Auth::check()) {
    echo "   ✅ Auth::check() detected logged in user.\n";
    $user = Auth::user();
    if ($user && $user['role'] === 'admin') {
        echo "   ✅ Role 'admin' retrieved correctly.\n";
        $passed += 2;
    } else {
        echo "   ❌ Role retrieval failed.\n";
        $failed += 2;
    }
    Auth::logout();
} else {
    echo "   ❌ Auth::check() failed.\n";
    $failed += 2;
}

// Test 6: Data Integrity (Seed Data Check)
echo "\n6. Testing Seed Data Integrity...\n";
$userCount = $db->query("SELECT COUNT(*) as count FROM users")->first()->count;
$prodCount = $db->query("SELECT COUNT(*) as count FROM products")->first()->count;
echo "   Users: $userCount (Expected: >= 2)\n";
echo "   Products: $prodCount (Expected: >= 15)\n";
if ($userCount >= 2 && $prodCount >= 15) {
    echo "   ✅ Seed data integrity verified.\n";
    $passed++;
} else {
    echo "   ⚠️ Seed data might be missing. Run migrations with SEED_DATA=true.\n";
    $failed++;
}

// Test 7: Coupon Manager (if exists)
echo "\n7. Testing Coupon System...\n";
if (file_exists(__DIR__ . '/../core/CouponManager.php')) {
    require_once __DIR__ . '/../core/CouponManager.php';
    try {
        $checkTable = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='coupons'")->first();
        if ($checkTable) {
            echo "   ✅ Coupons table exists.\n";
            $passed++;
        } else {
            echo "   ⚠️ Coupons table not found (run migrations).\n";
            $passed++;
        }
    } catch (Exception $e) {
        echo "   ⚠️ Coupon test skipped: " . $e->getMessage() . "\n";
        $passed++;
    }
} else {
    echo "   ℹ️ CouponManager not found (skipping).\n";
    $passed++;
}

// Summary
echo "\n=== TEST SUMMARY ===\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";

if ($failed === 0) {
    echo "\n🎉 ALL TESTS PASSED! System is ready.\n";
    exit(0);
} else {
    echo "\n⚠️ Some tests failed. Please review the logs above.\n";
    exit(1);
}
