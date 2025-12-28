remove current semester from profile in students.php(teacher)


login haru sabb milauna baki xa 
supabase snga sabb connection remaining


student_side ko every page kholda redirect to non exist page and
 ((Not Found
The requested URL was not found on this server.

Apache/2.4.58 (Win64) OpenSSL/3.1.3 PHP/8.2.12 Server at localhost Port 80))




sabbma  redirection link milauna parni raixa


in every page login check garna use

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if(!isset($_SESSION['id'])){
    header('Location:../project/login.php');
}


profile.php (student) ma edit ko function milauna baki
aaile lai clz ma dekhauna redirect ni garna parxa 




STUDENT SIDE
home done
attendance done
profile done 
logout done
results done





TEACHERS SIDE
students done
profile done
notices



