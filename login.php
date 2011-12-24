<? 
//session_save_path('/tmp');
session_start();
$conn= mysql_connect("localhost", "root", "");
mysql_select_db("journal",$conn);

if (isset($_POST['login']) && isset($_POST['passwd']))
{
    $login = mysql_real_escape_string($_POST['login']);
    $password = $_POST['passwd'];
    $query = "SELECT `id`
            FROM `users`
            WHERE `login`='{$login}' AND `password`='{$password}'
            LIMIT 1";
    $sql = mysql_query($query) or die(mysql_error());

    if (mysql_num_rows($sql) == 1) {
        $row = mysql_fetch_assoc($sql);
        $_SESSION['user_id'] = $row['id'];
echo "<HTML><HEAD>
<META HTTP-EQUIV=refresh CONTENT='0; url=journal.php'>
</HEAD></HTML>";
    }
    else {
        echo('///');
    }
}
include "login.html";
?>

