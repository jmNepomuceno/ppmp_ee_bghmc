<?php
include ('../session.php');
include('../assets/connection/sqlconnection.php');

$webservice = "http://192.168.42.10:8081/EmpPortal.asmx?wsdl";

if (isset($_POST["username"]) && isset($_POST["password"]) && trim($_POST["username"]) != "" && $_POST["password"] != "") {
    $biousername = $_POST["username"];
    $password = $_POST["password"];

    $param = array("bioUserName" => $biousername, "password" => $password, "accessMode" => 0);

    $soap = new SOAPClient($webservice);

    $result = $soap->AuthenticateEmployee($param)->AuthenticateEmployeeResult;

    // echo '<pre>';
    // var_dump($result);
    // echo '</pre>';

    $code = $result->Code;
    $canAccess = $result->CanAccess;
    $errorMessage = $result->Message;
    $userType = $result->UserType;


    if ($canAccess == 1) {
        if (isset($result->Account)) {
            $account = $result->Account;
            $name = $account->FirstName." ".substr($account->MiddleName,0,1).". ".$account->LastName;

            $_SESSION["user"] = $account->BiometricID;          
            $_SESSION["name"] = $account->FullName;
            $_SESSION["section"] = $account->Section;
            $_SESSION["sectionName"] = "";
            $_SESSION["division"] = $account->Division; 
            $_SESSION["password"] = $password;     
            $_SESSION["Authorized"] = "Yes";
            $_SESSION["role"] = "";
            $_SESSION["fetch_inventory"] = "";

            $admin_bioID = [3374, 3858, 2514];

            if(in_array($_SESSION["user"], $admin_bioID)){
                $_SESSION["role"] = 'admin';
            }else{
                $_SESSION["role"] = "user";
            }

            echo "/views/home.php";
        }
    }
    else {
        // Store error in session and redirect
        // header("location: ../php/login.php");
        // exit();
        echo "invalid";
    }

}
else {
    // header("location: ../php/login.php");
    // exit();
    // echo "<script>alert('Invalid username or password. Please try again.'); window.location.href = '../php/login.php';</script>";
    echo "invalid";
}


?>