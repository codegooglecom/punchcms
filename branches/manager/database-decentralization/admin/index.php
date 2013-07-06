<?php
error_reporting(E_ERROR|E_COMPILE_ERROR|E_CORE_ERROR);
require_once("includes/init.php");

$vars['submenuitems']=Array(
    Array('label'=> 'Browse', 'link'=>'?c=overview'),
    Array('label'=> 'Create', 'link'=>'?c=create'),
);


$myAccount= new Account();
$myAccount->setAccountName('Kaboebie');


if(isset($_SESSION['logindata']))
{
    $user = new User($_SESSION['logindata']['user_id']);
    
    $vars['login_username']=$user->getName();
    $vars['logout_url']='?c=logout';
    switch($_GET['c'])
    {
        case "logout":        
            $user->logout();
            System::redirect('?c=overview');
             break;        
        case "create": 
            echo $twig->render('create.twig', $vars);
            break;
        case "edit": 
            echo $twig->render('edit.twig', $vars);
            break;
        case "delete": 
            System::redirect('?c=overview');
            break;

        default: 
        $vars['accounts']=Accounts::getAccounts();
        echo $twig->render('overview.twig', $vars);
    }
}
else
{
    if(isset($_POST['command']) && $_POST['command']=="login")
    {
        $user = new User();
        if($user->login($_POST['username'], $_POST['password']))
        {
            System::redirect('?c=overview');
        }
        else
        {
            $vars['login']['error']='Login incorrect';
        }
    }   
    echo $twig->render('login.twig', $vars);
    echo Log::printDebug();
}



?>