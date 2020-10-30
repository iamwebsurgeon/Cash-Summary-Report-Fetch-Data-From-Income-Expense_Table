<?php
    include_once 'db.php';
    include_once 'settings.php';
?> 
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <title>Income Expense - Cash Summary Report</title>
  </head>
  <body>
        <div class="container pt-4  text-dark">
            <h3 class="pb-2 text-center">Fetching Data From Income and Expense Table - Cash Summary Report</h3>
            <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header"><i class="fa fa-table"></i> Cash Summary</div>
            <div class="row pt-3 pl-3">
              <div class="col-sm-12">
                <form action="" method="post">
                  <div class="row">
                    <div class="form-group col-md-3">
                      <label for="input-1">Starting Date</label>
                      <input type="date" name="starting-date" class="form-control" id="starting_date">
                    </div>

                    <div class="form-group col-md-3">
                      <label for="input-1">Ending Date</label>
                      <input type="date" name="ending-date" class="form-control" id="ending-date" >
                    </div>

                    <div class="col-md-3">
                      <br>
                      <button type="submit" name="get_report_btn" class="btn btn-success btn-lg"><i class="fa fa-check-square-o"></i>Go!</button>
                   </div>
                  </div>
                </form>
              </div>
            </div>


            <div class="card-body">
              

                <?php 

            $_SESSION['company_id'] = 1; 
              $sess_company_id = $_SESSION['company_id'];

             

              if(isset($_POST['get_report_btn'])){

                if(isset($_POST['starting-date'])){
                   $startingDate = $_POST['starting-date'];
                }
                if(isset($_POST['ending-date'])){
                  $endingDate = $_POST['ending-date'];
                }
                

                $totalQuery1 = "SELECT E.exp_date, SUM(E.amount) as total_expense, (SELECT IFNULL(SUM(I.amount), 0) FROM incomes I WHERE I.income_date = E.exp_date) as total_income, (SELECT (IFNULL(SUM(I.amount),0) - SUM(E.amount)) FROM incomes I WHERE E.exp_date = I.income_date) as cash_balance FROM expenses E";

                $conditions = array();
                  if(!empty($sess_company_id)){
                    $conditions[] = " E.company_id = '$sess_company_id'";
                  }
                  
                  if(!empty($startingDate) && !empty($endingDate)){
                    $conditions[] = " E.exp_date BETWEEN '$startingDate' AND '$endingDate'";
                  }
                  
                 $totalQuery = $totalQuery1;

                  if(count($conditions)>0){
                    $totalQuery .= " WHERE " . implode(' AND ', $conditions);
                  }
                  $totalQuery .= " GROUP BY E.exp_date";



                }
                else{
                    $totalQuery = "SELECT E.exp_date, SUM(E.amount) as total_expense, (SELECT IFNULL(SUM(I.amount), 0) FROM incomes I WHERE I.income_date = E.exp_date) as total_income, (SELECT (IFNULL(SUM(I.amount),0) - SUM(E.amount)) FROM incomes I WHERE E.exp_date = I.income_date) as cash_balance FROM expenses E WHERE E.exp_date = '$todayDate'";
                }

                  //echo '<script>alert("'.$totalQuery.'");</script>';


                  $runQueryTotal = mysqli_query($conn, $totalQuery);

                  if(mysqli_num_rows($runQueryTotal) > 0){



                // }

                ?>
                <div class="table-responsive">
                <table id="example" class="table table-bordered">
                <thead>
                  
                    <tr>
                        
                        <th>Date</th>
                        <th>Total Income</th>
                        <th>Total Expense</th>
                        <th>Cash Balance</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                    
                        <th>Date</th>
                        <th>Total Income</th>
                        <th>Total Expense</th>
                        <th>Cash Balance</th>

                    </tr>
                </tfoot>
                <tbody>

                  <?php
                    $sr = 1;
                    $grandTotalIncome = 0;
                    $grandTotalExp = 0;
                    while($row = mysqli_fetch_array($runQueryTotal)){
                    $expDate = $row['exp_date'];
                    $totalIncome = $row['total_income'];
                    
                    $totalExpense = $row['total_expense'];
                    $cashBalance = $row['cash_balance'];

                    $new_date = date("d-M-Y", strtotime($expDate));

                    $grandTotalIncome = $grandTotalIncome + $totalIncome;
                    $grandTotalExp =  $grandTotalExp + $totalExpense;

                    
                ?>
                    <tr>
                        
                        <td><?php if(isset($new_date)){echo $new_date; } ?></td>
                        <td><?php if(isset($totalIncome)){echo $totalIncome; } ?></td>
                        <td><?php if(isset($totalExpense)){echo $totalExpense; } ?></td>
                        <td><?php if(isset($cashBalance)){echo $cashBalance; } ?></td>
                    </tr>
                    <?php

                    $sr = $sr + 1;
                  }



                  ?>


                  <tr>
                  <td>Total</td>
                  <td><?php if(isset($grandTotalIncome)){echo $grandTotalIncome; } ?></td>
                  <td><?php if(isset($grandTotalExp)){echo $grandTotalExp; } ?></td>

                  <?php
                  if(isset($grandTotalExp) OR isset($grandTotalIncome)){
                    $grandTotal = $grandTotalIncome - $grandTotalExp;
                  }
                      

                  ?>
                  <td><?php if(isset($grandTotal)){echo $grandTotal; } ?></td>
                </tr>




                  
                

                </tbody>
                
            </table>
            </div>

            <?php
                      
                    }
                    else{
                       echo '
                        <div class="alert alert-warning alert-dismissible" role="alert">
                          <button type="button" class="close" data-dismiss="alert">&times;</button>
                          <div class="alert-icon">
                           <i class="fa fa-exclamation-triangle"></i>
                          </div>
                          <div class="alert-message">
                            <span><strong>Sorry!</strong> No Data Found</span>
                          </div>
                          </div>';
                              
                    }
                ?>


             
                

              
            
            </div>




          </div>
        </div>
      </div>
        </div>





    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>

    <!-- Option 2: jQuery, Popper.js, and Bootstrap JS
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.min.js" integrity="sha384-w1Q4orYjBQndcko6MimVbzY0tgp4pWB4lZ7lr30WKz0vr/aWKhXdBNmNb5D92v7s" crossorigin="anonymous"></script>
    -->
  </body>
</html>