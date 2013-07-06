<?php
error_reporting(E_ERROR);
require_once("includes/init.php");

$vars=Array();
    

if(isset($_SESSION['logindata']))
{
    $user = new User($_SESSION['logindata']['user_id']);
    
    $vars['submenuitems']=Array(
    Array('label'=> 'Browse', 'link'=>'?c=overview'),
    Array('label'=> 'Create', 'link'=>'?c=create'),
);
   
    $vars['login_username']=$user->getName();
    $vars['logout_url']='?c=logout';
    switch($_GET['c'])
    {
        case "logout":        
            $user->logout();
            System::redirect('?c=overview');
             break;        
        case "create": 
            if(isset($_POST['command']) && $_POST['command']=="save")
            {
                $account = new Account();
                $account->setAccountName($_POST['account_name']);
                $account->setSubdomain($_POST['subdomain']);
                $account->save(); 
                
                $account->setAdminName($_POST['admin_name']);
                $account->setAdminEmail($_POST['admin_email']);
                $account->setAdminPassword($_POST['admin_password']);
                $account->setSiteName($_POST['account_name']);
                
                System::redirect('?c=overview');
            }
            echo $twig->render('create.twig', $vars);
            break;
        case "edit": 
            if(isset($_POST['command']) && $_POST['command']=="save")
            {
                $account = new Account($_GET['aid']);
                $account->setAccountName($_POST['account_name']);
                $account->setAdminName($_POST['admin_name']);
                $account->setAdminEmail($_POST['admin_email']);
                $account->setAdminPassword($_POST['admin_password']);
                $account->save(); 
                $account->setSiteName($_POST['account_name']);
                
                System::redirect('?c=overview');
            }
            $myAccount = new Account($_GET['aid']);
            $vars['account']=$myAccount->toArray();
            echo $twig->render('edit.twig', $vars);
            break;
        case "delete": 
            $myAccount = new Account($_GET['aid']);
            $myAccount->delete();            
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
    //echo Log::printDebug();
}



?>