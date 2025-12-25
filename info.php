<?php
session_start();

// Check if the user is logged in by verifying session variables
if (isset($_SESSION["username"]) && isset($_SESSION['password'])) {
    $username = $_SESSION["username"];
    $password = $_SESSION["password"];

    // Include the info.php file to load the credentials
    include 'data/info.php';

    // Verify that the session username and password match the stored credentials
    if ($infoArray['username'] != $username || $infoArray['password'] != $password) {
        // If authentication fails, destroy the session and redirect to login
        session_destroy();
        header("Location: login.php");
        exit;
    }
} else {
    // If session variables are not set, redirect to login page
    header("Location: login.php");
    exit;
}
?><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>MARK WEB SCRIPT CHANGE </title>
<style>
body {
    font-family: Arial, sans-serif;
    background-color: black;
    color: white;
    margin: 0;
    padding: 20px;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    background-color: #222; /* Dark gray background */
    padding: 20px;
    border-radius: 5px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h2 {
    text-align: center;
    margin-top: 0;
}

form {
    margin-bottom: 20px;
}

label {
    display: block;
    margin-bottom: 5px;
}

select,
input[type="submit"] {
    width: 100%;
    padding: 10px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 5px;
    box-sizing: border-box;
    background-color: #333; /* Dark gray background */
    color: white;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}
 .blurred {
        color: green; /* Make text transparent */
        text-shadow: 0 0 5px rgba(0,111,0,0.5); /* Add a shadow effect to make it appear blurred */
    }
.message {
    margin-top: 20px;
    padding: 10px;
    border-radius: 5px;
}

.success {
    background-color: #d4edda;
    color: #155724;
}

.error {
    background-color: #f8d7da;
    color: #721c24;
}
</style>
</head>
<body>

<div class="container">
<h2 class="blurred">MARK WEB SCRIPT CHANGE</h2>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
  <input type="hidden" name="delete" value="true">
  <input type="submit" value="Remove Old Script" name="submit">
</form>

<h5>  Note: Before making any changes to the Script, please ensure to click on "Remove Old Script" </h5>

<script>
// Your script goes here
</script>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
  <label for="zipfile">Select a Script file:</label><br>
  <select name="zipfile" id="zipfile">
    <?php
function listFiles($folder) {
    // Get the list of files in the specified folder
    $files = glob($folder . '/*.zip');
    // Sort files by name in descending order
    usort($files, function($a, $b) {
        return basename($b) <=> basename($a);
    });
    return $files;
}

// List files in "allscript" folder
$allscriptFiles = listFiles('../allscript');
// List files in "markscript" folder
$markscriptFiles = listFiles('../markscript');

echo '<optgroup label="Allscript Files">';
foreach ($allscriptFiles as $file) {
    $filename = basename($file);
    echo "<option value='$filename'>$filename</option>";
}
echo '</optgroup>';

echo '<optgroup label="Markscript Files">';
foreach ($markscriptFiles as $file) {
    $filename = basename($file);
    echo "<option value='$filename'>$filename</option>";
}
echo '</optgroup>';
?>

  </select><br><br>
  <input type="submit" value="Extract" name="submit">
  
</form>

<form action="panel.php" method="get">
    <input type="submit" value="Go to Panel">
</form>

<?php
if(isset($_POST['submit'])) {
    if(isset($_POST['delete']) && $_POST['delete'] === 'true') {
        // Function to recursively delete files and directories
        function deleteFilesAndFolders($dir) {
            // Open the directory
            $handle = opendir($dir);

            // Loop through the directory
            while (false !== ($item = readdir($handle))) {
                if ($item != "." && $item != "..") {
                    if (is_dir($dir . "/" . $item)) {
                        // If it's a directory, call the function recursively
                        deleteFilesAndFolders($dir . "/" . $item);
                    } else {
                        // If it's a file, delete it
                        if (unlink($dir . "/" . $item)) {
                            echo "<div class='message success'>File $item deleted successfully.</div>";
                        } else {
                            echo "<div class='message error'>Error deleting file $item.</div>";
                        }
                    }
                }
            }

            // Close the directory handle
            closedir($handle);

            // Attempt to delete the directory itself
            if (rmdir($dir)) {
                echo "<div class='message success'>Directory $dir deleted successfully.</div>";
            } else {
                echo "<div class='message error'>Error deleting directory $dir.</div>";
            }
        }

        // Specify the path to the OFFER folder
        $folder_path = './OFFER/';

        // Call the function to delete files and folders recursively
        deleteFilesAndFolders($folder_path);

        // Check if the OFFER folder is deleted
        if (!is_dir($folder_path)) {
            echo "<div class='message success'>All files and folders in OFFER folder have been deleted.</div>";
        } else {
            echo "<div class='message error'>Some files or folders in OFFER folder could not be deleted.</div>";
        }
    } else {
        // Check if form is submitted
        if(isset($_POST['submit'])) {
            // Get selected zip file
            $zipfile = $_POST['zipfile'];

            // Delete existing files in the OFFER folder
            $files = glob('OFFER/*'); // Get all files in the OFFER folder
            foreach($files as $file) {
                if(is_file($file))
                    unlink($file); // Delete file
            }

            // Extract from both "allscript" and "markscript" directories
            if (file_exists('../allscript/' . $zipfile)) {
                $zipFilePath = '../allscript/' . $zipfile;
            } elseif (file_exists('../markscript/' . $zipfile)) {
                $zipFilePath = '../markscript/' . $zipfile;
            } else {
                echo "<div class='message error'>Selected Script file does not exist.</div>";
                exit;
            }

            // Create a new ZipArchive object
            $zip = new ZipArchive;
            // Open the zip file
            if ($zip->open($zipFilePath) === TRUE) {
                // Extract the contents to the specified folder (OFFER folder)
                $zip->extractTo('OFFER/');
                // Close the zip file
                $zip->close();
                echo "<div class='message success'>Script file extracted successfully.</div>";
            } else {
                echo "<div class='message error'>Failed to extract Script file.</div>";
            }
        }
    }
}
?>

</div>
</body>
</html>
