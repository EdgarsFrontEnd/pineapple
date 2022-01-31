<?php // export CSV functionality
            function exportToCsv(){
                include 'connection.php';
                  $filename = "emails_".date("d.m.Y").".csv";
                  $csv_file = fopen('php://output', 'w');
                  
                  header('Content-type: application/csv');
                  header('Content-Disposition: attachment; filename="'.$filename.'"');
              
                // if not checked exports all data
                if(isset($_GET['selected'])){
                  $id_list = array();
                  foreach($_GET['selected'] as $val){
                    $id_list[] = (int) $val;
                  }
                $id_list = implode(',', $id_list);
                $sql = "select * from pineapple  WHERE id_number IN ($id_list)";
                }else{
                  $sql = "select * from pineapple";
                }
                $query = mysqli_query( $connection, $sql);
                $results = mysqli_fetch_all($query, MYSQLI_ASSOC);
                mysqli_free_result($query);
              
                  $header_row = array("Id_number", "Email", "Date");
                  fputcsv($csv_file,$header_row,',','"');
                  
                  foreach($results as $result){
                      $row = array(
                        $result['id_number'],
                        $result['email'],
                        $result['date'],
                      );
                      
                      fputcsv($csv_file,$row,',','"');
                  }
                  fclose($csv_file);
                exit; // this took me a while to figure out why html code is getting exported aswell
              }
              
              if(isset($_GET['export'])){
                 exportToCsv();
              }

              ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="./tablestyle.css" />
    <title>Email list</title>
  </head>
  <body>
      <h1>Email list</h1>
      
      <form action="" method="GET">
                <!-- controls -->
        <?php 
            include 'connection.php';
            $page_id = isset($_GET['page_id']) ? mysqli_real_escape_string($connection, $_GET['page_id']) : 1;
            $page_id = intval( $page_id );
            if(!isset($_GET['submit'])){
              header("Location: table.php?sort-by=date&email-domain=emails&custom-search=&submit=apply+changes&page_id=$page_id");
              die();
            }

            echo "<div class='options'>";
            // export
            echo "<input type='submit' name='export' value='export CSV'/>";

            // delete
            echo "<input type='submit' name='delete' value='delete selected'/>";

            // sort by
            echo "<select name='sort-by'>";
            echo "<option value='date'>date</option>";
            echo "<option value='email'>name</option>";
            echo "</select>";

            // select email domain
            // $query = mysqli_query( $connection, "SELECT DISTINCT SUBSTRING_INDEX(email, '@', -1) FROM pineapple"); does not work in php
            // i had to take a bit harder approach
            $query = mysqli_query( $connection, "SELECT DISTINCT email from pineapple");
            $emails = mysqli_fetch_all($query, MYSQLI_ASSOC);
            mysqli_free_result($query);
            $unique_email_domains = array();
            foreach($emails as $email){
                preg_match('/(?<=@)[a-zA-z0-9]*(?<!\.)/', $email['email'], $e );
                array_push($unique_email_domains, $e[0]);
            };
            echo "<select name='email-domain'>";
            echo "<option value='emails'>Choose email to filter</option>";

            $unique_email_domains = array_unique($unique_email_domains);
            foreach($unique_email_domains as $domain){
                echo "<option value='" . $domain . "'>" . $domain . "</option>";
            }
            echo "</select>";

            // custom search
            echo "<input type='text' name='custom-search' value='' placeholder='search email'>";

            // submit button
            echo "<input type='submit' name='submit' value='apply changes'>";

            echo "</div>";
        ?>
<!-- table results -->
        <table>
          <tr>
            <td>Email ID</td>
            <td>Email</td>
            <td>Date</td>
            <td>Select</td>
          </tr>
          <?php
          
        // generating sql query based on selected filters
        if(isset($_GET['submit']) || isset($_GET['delete'])){
            $selected_sort = $_GET['sort-by'];

            if($_GET['email-domain'] == 'emails'){
                $selected_email_domain = "";
            }else{
                $selected_domain = $_GET['email-domain'];
                $selected_email_domain =  "WHERE email LIKE '%$selected_domain%'";
            }

            $custom_search = $_GET['custom-search'];
            if($_GET['custom-search'] == ""){
                $custom_search_value = "";
            }else if($_GET['email-domain'] == 'emails'){
                $custom_search_value = "WHERE email LIKE '%$custom_search%'";
            }else{
                $custom_search_value = "AND email LIKE '%$custom_search%'";
            }

            $offset = $page_id * 10 -10;
            $sql = "SELECT * FROM pineapple $selected_email_domain $custom_search_value ORDER BY $selected_sort LIMIT 10 OFFSET $offset";
            $num_rows = mysqli_num_rows (mysqli_query($connection, "SELECT * FROM pineapple $selected_email_domain $custom_search_value"));
        }

        // executing our dynamic query
        $query = mysqli_query( $connection, $sql);
        $emails = mysqli_fetch_all($query, MYSQLI_ASSOC);
        mysqli_free_result($query);

        foreach($emails as $email){
            echo "<tr>";
            echo "<td>" . $email['id_number'] . "</td>";
            echo "<td>" . $email['email'] . "</td>";
            echo "<td>" . $email['date'] . "</td>";
            $checkbox_id = $email['id_number'];
            echo "<td><input type='checkbox' name='selected[]' id='selected' value='$checkbox_id'/></td>";
            echo "</tr>";
        };
            mysqli_close($connection);
          ?>
        </table>
      </form>

      <?php 
        // some crazy linking
        $current_link = htmlspecialchars("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
            
            if(!isset($_GET['page_id'])){
              $path = "?page_id=";
              if(isset($_GET['submit']) || isset($_GET['delete']) || isset($_GET['export'])){
                $path = "&page_id=";
              }
            }else{
              $path = "";
              $page_id_digits = strlen((string)$page_id);
              $current_link = substr_replace($current_link, "", -$page_id_digits);
            }

              // prev
              if($page_id>1){
              echo "<a href='" . $current_link . $path . ($page_id-1) . "'>prev</a>";
              }
              
              // next
              if($num_rows>$page_id*10){
              echo "<a href='" . $current_link . $path . ($page_id+1) . "'>next</a>";
              } 
            

            // delete rows
            if(isset($_GET['delete'])){
                 deleteRows();
              }

            function deleteRows(){
                include 'connection.php';
                if(isset($_GET['selected'])){
                    $id_list = array();
                    foreach($_GET['selected'] as $val){
                      $id_list[] = (int) $val;
                    }
                  $id_list = implode(',', $id_list);
                  $sql = "DELETE FROM pineapple WHERE id_number IN ($id_list)";
                  if ($connection->query($sql) === TRUE) {
                    header('table.php');
                  } else {
                    echo "Error deleting record: " . $connection->error;
                  }
                 }
            }
        ?>
  </body>
</html>