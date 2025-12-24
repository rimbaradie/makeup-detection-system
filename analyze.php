<?php
include 'connect.php';

$error = '';  // Initialize error variable

// Harmful ingredients list
$harmful_ingredients = [
    'paraben', 'sulfate', 'phthalate', 'formaldehyde', 
    'fragrance', 'alcohol', 'mineral oil', 'triclosan', 
    'benzophenone', 'talc'
];

// Function to analyze ingredients
function analyzeIngredients($input, $harmful_ingredients) {
    $input = strtolower($input);
    $ingredients = preg_split("/[\r\n,]+/", $input);
    
    $detected = [];
    foreach ($ingredients as $ingredient) {
        $ingredient = trim($ingredient);
        foreach ($harmful_ingredients as $harmful) {
            if (strpos($ingredient, $harmful) !== false) {
                $detected[] = $harmful;
            }
        }
    }
    return array_unique($detected);
}

// Safety score calculation
function calculateSafetyScore($detected, $total) {
    if ($total == 0) return 100;
    $count = count($detected);
    $score = 100 - ($count / $total * 100);
    return max(0, round($score));
}

// Fetch ingredient list from DB by product name
function getProductIngredients($conn, $product_name) {
    $sql = "SELECT ingredient_list FROM makeup_products WHERE product_name = :name LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':name', $product_name);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['ingredient_list'] : null;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = trim($_POST['product_name'] ?? '');
    
    if (!$product_name) {
        $error = "Please enter a product name.";
    } else {
        $ingredient_list = getProductIngredients($conn, $product_name);
        
        if (!$ingredient_list) {
            $error = "Product not found in the database.";
        } else {
            $detected = analyzeIngredients($ingredient_list, $harmful_ingredients);
            $ingredients_count = count(preg_split("/[\r\n,]+/", strtolower($ingredient_list)));
            $score = calculateSafetyScore($detected, $ingredients_count);

            if ($score > 80) {
                $assessment = "This product appears safe based on ingredient analysis.";
            } elseif ($score > 50) {
                $assessment = "This product contains some potentially harmful ingredients. Use with caution.";
            } else {
                $assessment = "Warning: This product contains several harmful ingredients. Avoid if possible.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Makeup Product Safety Analysis</title>
<style>
    /* Reset some styles */
    body, h1, h2, p, pre, form {
        margin: 0;
        padding: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
        background: #f8f5f2;
        color: #333;
        padding: 30px;
        max-width: 700px;
        margin: auto;
    }
    h1 {
        color: #b35081;
        margin-bottom: 20px;
        text-align: center;
        font-weight: 700;
    }
    form {
        background: #fff;
        padding: 20px 25px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(179, 80, 129, 0.3);
        margin-bottom: 30px;
    }
    label {
        font-weight: 600;
        display: block;
        margin-bottom: 8px;
        color: #7a3e5b;
    }
    input[type="text"] {
        width: 100%;
        padding: 10px 12px;
        font-size: 1rem;
        border: 2px solid #b35081;
        border-radius: 6px;
        box-sizing: border-box;
        margin-bottom: 20px;
        transition: border-color 0.3s ease;
    }
    input[type="text"]:focus {
        border-color: #853963;
        outline: none;
    }
    input[type="submit"] {
        background-color: #b35081;
        color: white;
        font-weight: 700;
        padding: 12px 25px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
    }
    input[type="submit"]:hover {
        background-color: #853963;
    }
    p.error {
        color: #d9534f;
        font-weight: 600;
        margin-bottom: 20px;
        text-align: center;
    }
    h2 {
        color: #b35081;
        margin-bottom: 10px;
        font-weight: 700;
        text-align: center;
    }
    pre {
        background-color: #fff0f5;
        padding: 15px;
        border-radius: 6px;
        border: 1px solid #e6cfe5;
        font-size: 0.95rem;
        overflow-x: auto;
        margin-bottom: 15px;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    p strong {
        color: #7a3e5b;
    }
    .results {
        background: #fff;
        padding: 20px 25px;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(179, 80, 129, 0.3);
    }
</style>
</head>
<body>
    <h1>Makeup Product Safety Analysis</h1>
    
    <form method="post" action="">
        <label for="product_name">Enter Product Name:</label>
        <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($_POST['product_name'] ?? '') ?>" required>
        <input type="submit" value="Analyze">
    </form>

    <?php if (!empty($error)): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if (isset($ingredient_list) && !$error): ?>
        <div class="results">
            <h2>Analysis for: <?php echo htmlspecialchars($product_name); ?></h2>
            <p><strong>Ingredients:</strong></p>
            <pre><?php echo htmlspecialchars($ingredient_list); ?></pre>

            <p><strong>Detected Harmful Ingredients:</strong> 
                <?php echo count($detected) ? implode(', ', $detected) : 'None'; ?>
            </p>
            <p><strong>Safety Score:</strong> <?php echo $score; ?>/100</p>
            <p><strong>Dermatologist Assessment:</strong> <?php echo $assessment; ?></p>
        </div>
    <?php endif; ?>
</body>
</html>
