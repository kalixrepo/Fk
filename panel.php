<?php
session_start();

if (isset($_SESSION["username"]) && isset($_SESSION['password'])) {
    $username = $_SESSION["username"];
    $password = $_SESSION["password"];

    // Include the info.php file
    include 'data/info.php';  // This loads the $infoArray variable from info.php
    
    // Load info.json for name and email
    $infoJson = json_decode(file_get_contents('data/info.json'), true);

    // Check if the username and password match
    if ($infoArray['username'] == $username && $infoArray['password'] == $password) {

        // Handle different POST requests for info.json fields
        if (isset($_POST["senderName"])) {
            $infoJson["name"] = $_POST["senderName"];
            file_put_contents('data/info.json', json_encode($infoJson, JSON_PRETTY_PRINT));
        } elseif (isset($_POST["receiverMail"])) {
            $infoJson["email"] = $_POST["receiverMail"];
            file_put_contents('data/info.json', json_encode($infoJson, JSON_PRETTY_PRINT));
        } elseif (isset($_POST["password"])) {
            // Update the password in the array
            $infoArray["password"] = $_POST["password"];
            
            // Save the updated information back to info.php
            $updateContent = '<?php $infoArray = ' . var_export($infoArray, true) . '; ?>';
            file_put_contents('data/info.php', $updateContent);
            
            // Destroy the session after updating the password
            session_destroy();
            
            // Redirect to login page
            header('Location: login.php');
            exit; // Ensures no further code is executed after redirect
        }

        // Save updates back to info.php (if not password change)
        if (!isset($_POST["password"])) {
            $updateContent = '<?php $infoArray = ' . var_export($infoArray, true) . '; ?>';
            file_put_contents('data/info.php', $updateContent);
        }

    } else {
        session_destroy(); // Destroy session if authentication fails
        header("Location:login.php"); // Redirect to login page
        exit; // Ensures no further code is executed after redirect
    }
} else {
    header("Location: login.php");
    exit; // Ensures no further code is executed if session is not set
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MARK-WEB-PANEL</title>
    <link rel="stylesheet" href="panel-css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .container .form .headings{
            text-align: center;
            padding: 0 50px;
        }

        .boxes{
            width: 100%;
            height: auto;
            margin: 0 auto;
        }
        .boxes .box{
            width: 100%;
            height: 40PX;
            background-color: rgb(0, 162, 255);
            margin: 4px 0;
            display: flex;
            position: relative;
        }

        .boxes .box .left-span{
            width: 32px;
            height: 100%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-right: 1px solid white;
        }
        .boxes .box .right-span{
            width: 32px;
            height: 100%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-left: 1px solid white;
            position: absolute;
            right: 0;
            cursor: pointer;
        }

        .boxes .box p{
            max-width: calc(100% - 0px);
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            margin: auto 2%;
            color: white;
            font-weight: bold;
            overflow-y: auto;
        }

        .boxes .box p::-webkit-scrollbar{
            height: 1px;
        }

        label{
            color: white;
            font-family: 'Courier New', Courier, monospace;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
            position: relative;
            top: 4px;
            letter-spacing: 2px;
        }
        .edit-field{
            width: calc(100% - 80px);
            background-color: transparent;
            padding: 0 4px;
            border: 0;
            outline: 0;
            color: white;
        }

        .edit-field::placeholder{
            color: white;
        }

        form{
            display: none;
        }

        .saveOption{
            background-color: #5cb85c;
            cursor: pointer;
        }

        .brandings{
            color: white;
            text-align: center;
            font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
        }

        .glow{
            animation: glow 3s infinite linear;
            text-shadow: 0 0 20px currentColor;
            transition: 0.4s;
        }

        @keyframes glow {
            0%{
                color: red;
            }
            10%{
                color: rgb(255, 0, 238);
            }
            20%{
                color: yellow;
            }
            30%{
                color: orange;
            }
            40%{
                color: rgb(68, 255, 0);
            }
            50%{
                color: rgb(0, 255, 234);
            }
            60%{
                color: rgb(0, 64, 255);
            }
            70%{
                color: rgb(0, 247, 255);
            }
            80%{
                color: rgb(0, 251, 255);
            }
            90%{
                color: rgb(157, 0, 255);
            }
            100%{
                color: rgb(255, 0, 187);
            }
        }

        .alretBox{
            width: 100%;
            height: auto;
            color:white;
        }
        .alert{
            text-align: center;
            font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
            padding: 6px 0;
            font-size: 1rem;
        }
        .success{
            background:green;
        }
        .failed{
            background:red;
        }

        .changePass{
            padding: 4px 0;
            color:white;
            background:red;
            border: 1px solid white;
            outline: none;
            cursor: pointer;
        }
        .SCRIPTCHANGE{
            padding: 4px 0;
            color:white;
            background:GREEN;
            border: 1px solid white;
            outline: none;
            cursor: pointer;
        }
        
        .out{
             padding: 4px 0;
    color: #fff; /* White text color */
    background: black; /* Black background */
    border: 1px solid #fff;
    outline: none;
    cursor: pointer;
        }
        #passBox , .passLabel{
            display:none ;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form"><br>
            <hr>
            <div class="headings">
                <h2 class="glow" style="font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;color:#fff">Hii! <?php echo $username ?></h2>
                <h4 class="glow" style="font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;color:#fff">Welcome Back!</h4>
            </div>
            <hr><br>
            
            <div class="boxes">
                <label>UPI ID</label>
                <div class="box">
                    <p class="resultNameHide"><?php echo $infoJson['name'] ?></p>
                    <form id="saveNameForm" method="post">
                        <input type="text" required class="edit-field" placeholder="ENTER UPI ID" name="senderName">
                        <span class="right-span saveOption"><button type="submit" style="width: 100%;height: 100%;color: white;background-color: transparent;outline: none;border: none;"><i class="bi bi-floppy-fill"></i></button></span>
                    </form>
                    <span class="right-span resultNameHide" onclick="editOpen('name')"><i class="bi bi-pencil-square"></i></span>
                </div>

                <label>TR</label>
                <div class="box">
                    <p class="resultMailHide"><?php echo $infoJson['email'] ?></p>
                    <form id="saveMailForm" method="post">
                        <input type="text" required class="edit-field" placeholder="ENTER UPI TR" name="receiverMail">
                        <span class="right-span saveOption"><button type="submit" style="width: 100%;height: 100%;color: white;background-color: transparent;outline: none;border: none;"><i class="bi bi-floppy-fill"></i></button></span>
                    </form>
                    <span class="right-span resultMailHide" onclick="editOpen('email')"><i class="bi bi-pencil-square"></i></span>
                </div>

                <label class="passLabel">NEW PASSWORD</label>
                <div class="box" id="passBox">
                    <span class="left-span"><i class="bi bi-lock-fill"></i></span>
                    <form id="savePass" style="display: contents;" method="post">
                        <input type="password" required class="edit-field" placeholder="Enter New Password" name="password">
                        <span class="right-span saveOption"><button type="submit" style="width: 100%;height: 100%;color: white;background-color: transparent;outline: none;border: none;"><i class="bi bi-floppy-fill"></i></button></span>
                    </form>
                </div>
            </div>
            
            <button class="changePass" onclick="editOpen('password')">Change Password</button>
            <button class="SCRIPTCHANGE" onclick="window.location.href = 'info.php'">Script Change</button><br>
            <button class="out" onclick="window.location.href = 'logout.php'">Logout</button><br>
            <div class="brandings">
                <h2><span style="text-decoration: underline;color: aquamarine;"> Thanks </span><span style="text-decoration: underline;color: #3aff00;"> For </span><span style="text-decoration: underline;color: aqua;"> Buying </span></h2>
                <h3 class="glow">MARK WEB PANEL</h3><br><br>
                <p>Need Help? <span><a style="color: aqua;" href="https://telegram.me/IAMMARKOP">Contact Me</a></span></p>
                <a style="color: aqua;" href="https://telegram.me/MARKSCRIPT">Join For Updates</a>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script>
        let editOpen = (option) => {
            if (option === 'name') {
                $('.resultNameHide').hide();
                $('#saveNameForm').css('display', 'contents');
            } else if (option === 'email') {
                $('.resultMailHide').hide();
                $('#saveMailForm').css('display', 'contents');
            } else if (option === 'password') {
                $('#passBox').css('display', 'flex');
                $('.passLabel').css('display', 'inline');
            }
        }
    </script>
</body>
</html>