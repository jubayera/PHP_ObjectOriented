<?php


//Test this in http://www.practical-research.org/prob3.php/TransactionStats

class TransactionStatsClass
{
  public $UserId;
  public $TransactionCount;
  public $CurrencySum;
}

$splitted = explode('/', $_SERVER['PHP_SELF']);
if( $splitted[2] == "TransactionStats") 
{
      echo '<form name="postform" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">
                <table id="postarea">
                <tbody>                  
                    <tr>    <td>User ID:</td><td><input type="text" name="UserID"></td></tr>                    
                    <tr>    <td></td><td> <input type="submit" value="Submit Entry"> </td>    </tr>
                </tbody>
                </table>
            </form>';

      if(isset($_POST['UserID']))
      {
          $myObjTransaction_Encode = new TransactionStatsClass;

          $myObjTransaction_Encode->UserId = $_POST['UserID'];

          $transactionJSON = json_encode($myObjTransaction_Encode);
          //echo $transactionJSON;

          $myObjTransaction_Decode = new TransactionStatsClass;
          $myObjTransaction_Decode = json_decode($transactionJSON);
           
          //is_a($myObjTransaction_Decode, 'TransactionStatsClass') 
      }
      else
      {
            echo 'Can accept RAW JSON Input, if not posted through form. This form can accept input through form submission'; 

            //Make sure that it is a POST request.
            if(strcasecmp($_SERVER['REQUEST_METHOD'], 'POST') != 0){
                throw new Exception('Request method must be POST!');
            }

            //Make sure that the content type of the POST request has been set to application/json
            $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
            /*if(strcasecmp($contentType, 'application/json') != 0){
                throw new Exception('Content type must be: application/json');
            }*/
             
            //Receive the RAW post data.
            $content = trim(file_get_contents("php://input"));
                  
            //Attempt to decode the incoming RAW post data from JSON.
            $myObjTransaction_Decode = json_decode($content, true);
             
            //If json_decode failed, the JSON is invalid.
            if(!is_array($myObjTransaction_Decode)) {
                throw new Exception('Received content contained invalid JSON!');
            }
        }

              $servername = localhost;
              $db_name = "practic3_transaction";
              $db_user = "practic3_user";
              $db_password = 'test*server*games';
              // Create connection
              $conn = new mysqli($servername, $db_user, $db_password, $db_name);
              // Check connection
              if ($conn->connect_error) {
                  die("Connection failed: " . $conn->connect_error);
              } 
              
              $sql = "SELECT SUM(CurrencyAmount) AS CurrencyAmountSUM, COUNT(UserId) as transactions FROM Prob2_Transaction WHERE UserId = " . $myObjTransaction_Decode->{'UserId'};
              $result = $conn->query($sql);

              if (!$result) {
                  echo "Could not successfully run query ($sql) from DB: " . mysql_error();
                  exit;
              }

              while($row = $result->fetch_array())
              {
                $rows[] = $row;
              }

              foreach($rows as $row)
              {
                  $myObjTransaction_Encode = new TransactionStatsClass;

                  $myObjTransaction_Encode->UserId = $myObjTransaction_Decode->{'UserId'};
                  
                  $myObjTransaction_Encode->TransactionCount = $row["transactions"];

                  $myObjTransaction_Encode->CurrencySum = $row["CurrencyAmountSUM"];

                  $transactionJSON = json_encode($myObjTransaction_Encode);
            
                  echo $transactionJSON;
                  
              }

              /* free result set */
              $result->close();

              $conn->close();
  }      

?>
