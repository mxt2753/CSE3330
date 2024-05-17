

<?php
//Name: Mary-Rose Tracy
//ID#: 1001852753
//Name: Alisa Williams 
//ID#:1001925705 
//DB starters to get us going
$dbHost="localhost"; 
$dbUser="root";
$dbPassword=""; 
$dbName="wholeorganicfoodshop";
$databaseConnection=new mysqli($dbHost,$dbUser,$dbPassword,$dbName);
if($databaseConnection -> connect_error)
{
    die("Failed to connect to database: ".$databaseConnection -> connect_error);
}
//Q1. Display the ITEM details based on any one of the following: Item name or Item Id.
function performItemSearch($dbConn, $itemSearch)
{
    $query=$dbConn -> prepare("SELECT*FROM ITEM WHERE Iname LIKE CONCAT('%',?,'%') OR iId= ?");
    $query -> bind_param("ss",$itemSearch,$itemSearch);
    $query ->execute();
    $results=$query -> get_result();
    if($results -> num_rows>0)
    {
        echo"<h2>Item Search Results:</h2>";
        while ($item = $results ->fetch_assoc())
        {
            echo"Item ID: ".$item["iId"]." - Name: " .$item["Iname"]." - Price: $".$item["Sprice"]."<br>";
        }
    }
    else
    {
        echo"No matching items found.";
    }
    $query ->close();
}

//Q2. Insert a new item “Brussels” in the database using the web interface you created.
function addItem($dbConn, $itemId, $itemName, $itemPrice)
{
    $query=$dbConn ->prepare("SELECT iId FROM ITEM WHERE iId = ?");
    $query ->bind_param("s",$itemId);
    $query ->execute();
    if
    ($query -> get_result() ->num_rows>0)
    {
        echo "Error: Item ID already in use.";
    }
    else 
    {
        $query=$dbConn ->prepare("INSERT INTO ITEM (iId, Iname, Sprice) VALUES (?,?,?)");
        $query ->bind_param("ssd",$itemId,$itemName,$itemPrice);
        if
        ($query ->execute())
        {
            echo "Successfully added item: $itemId - $itemName at $$itemPrice";
        } 
        else
        {
            echo "Failed to insert item: " .$query ->error;
        }
    }
    $query ->close();
}

//Q3. Update the item that you just added “Brussels” to “Brussel Sprouts” using the web interface you created
function updateItemDetails($dbConn,$currentItemName,$newItemName) 
{
    $query=$dbConn ->prepare("UPDATE ITEM SET Iname = ? WHERE Iname = ?");
    $query ->bind_param("ss",$newItemName,$currentItemName);
    if
    ($query->execute()) 
    {
        echo "Successfully updated item: $currentItemName to $newItemName";
    } 
    else 
    {
        echo "Failed to update item: " . $query ->error;
    }
    $query->close();
}
//Q4. Delete the item record for “Brussel Sprouts” that you just added using the web interface you created.
function removeItem($dbConn,$itemName) 
{
    $query=$dbConn ->prepare("DELETE FROM ITEM WHERE Iname = ?");
    $query ->bind_param("s", $itemName);
    if
    ($query->execute()) 
    {
        echo "Successfully deleted item: $itemName";
    } 
    else
    {
        echo "Failed to delete item: " . $query ->error;
    }
    $query ->close();
}
//We need to allow multiple requests to be on the user. so yes
if($_SERVER["REQUEST_METHOD"]==="GET"&& isset($_GET['search_item']))
{
    performItemSearch($databaseConnection,$_GET['search_item']);
}

if($_SERVER["REQUEST_METHOD"]==="POST")
{
    if(isset($_POST['insert_item'])) 
    {
        addItem($databaseConnection, $_POST['new_item_id'], $_POST['new_item_name'], $_POST['new_item_price']);
    }
    if(isset($_POST['update_item']))
    {
        updateItemDetails($databaseConnection, $_POST['old_item_name'], $_POST['new_item_name']);
    }
    if(isset($_POST['delete_item']))
    {
        removeItem($databaseConnection, $_POST['delete_item_name']);
    }
}

$databaseConnection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Item Management</title>
    <style>
        body 
        {
            background-color: #add8e6; 
            /* Light blue color */
        }
        /* Helpful with aesthetics */
        form
        {
            margin-bottom:20px;
            padding:20px;
            background-color:white; 
            /* White background just like for google forums */
            border-radius:8px;
            box-shadow:0 2px 4px rgba(0,0,0,0.1);
        }
        input[type=text],input[type=submit]
        {
            padding:8px;
            margin-top:5px;
            margin-bottom:10px;
            border:1px solid #ccc;
            border-radius:4px;
        }
        input[type=submit]
        {
            background-color: #4CAF50; 
            /* Green color- buttons */
            color:white;
            cursor:pointer;
        }
        input[type=submit]:hover
        {
            background-color:#45a049;
        }
        label
        {
            display:block;
            margin-top:20px;
        }
        /*Now background stuff for the other things. */
    </style>
</head>
<body>
    <h2>Search Item:</h2>
    <form action="" method="GET">
        <label for="search_item">Search Item:</label>
        <input type="text" id="search_item" name="search_item">
        <input type="submit" value="Search">
    </form>

    <h2>Insert Item:</h2>
    <form action="" method="POST">
        <label for="new_item_id">Item ID:</label>
        <input type="text" id="new_item_id" name="new_item_id" required>
        <label for="new_item_name">New Item Name:</label>
        <input type="text" id="new_item_name" name="new_item_name" required>
        <label for="new_item_price">Price:</label>
        <input type="text" id="new_item_price" name="new_item_price" required>
        <input type="submit" value="Insert Item" name="insert_item">
    </form>

    <h2>Update Item:</h2>
    <form action="" method="POST">
        <label for="old_item_name">Old Item Name:</label>
        <input type="text" id="old_item_name" name="old_item_name">
        <label for="new_item_name">New Item Name:</label>
        <input type="text" id="new_item_name" name="new_item_name">
        <input type="submit" value="Update Item" name="update_item">
    </form>

    <h2>Delete Item:</h2>
    <form action="" method="POST">
        <label for="delete_item_name">Item Name to Delete:</label>
        <input type="text" id="delete_item_name" name="delete_item_name">
        <input type="submit" value="Delete Item" name="delete_item">
    </form>
</body>
</html>
