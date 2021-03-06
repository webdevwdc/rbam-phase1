<?php ob_start ();
session_start();
include("conn.php");
check_login();
include("findUsageHistory.php");
	
if(getRoleAccessStatusByUser('USAGE_HISTORY',$_SESSION['user_id'])!='Y')
{
	   header('location:index.php');
}
	
?>
<?php
	$LoginUserId = 1;
	$PageName = "usage-history.php";
	
	$sort =  isset( $_GET['sort'] )? $_GET['sort']:'desc';
	$OrderBY = "";
	$FieldName = "";
	
	$OrderBY = "asc";
	$FieldName = "USER_NAMES";
	$Sorts = "";
	$Sorts = isset( $_POST['h_field_order'] )? $_POST['h_field_order']: $sort;
	$FileName = isset( $_POST['h_field_name'] )? $_POST['h_field_name']: $FieldName;
	//$IsUpdate = isset( $_POST['h_field_update'] )? $_POST['h_field_update']: "";
	if ($Sorts == 'asc')
	{
   	 $OrderBY = " desc";
   	 $FieldName = $FileName;
	}
	if ($Sorts == 'desc')
	{
		 $OrderBY = " asc";
		 $FieldName = $FileName;
	}

	$SQueryOrderBy = " order by $FieldName $OrderBY";
	$record_per_page=$RecordsPerPage;
	
	$insArr = array();
	
	$TotalRows = isset($_REQUEST['h_NumRows'])?$_REQUEST['h_NumRows']:0;
	


   if (isset($_GET["page"]))
	{
		$page  = $_GET["page"];
	}
	else
	{
		$page=1;
	}
	$start_from = ($page-1) *  $record_per_page;
?>
<script type="text/javascript">
function DataSort(str1,str2)
{
	var str3;
	document.getElementById('h_field_name').value = str1;
	document.getElementById('h_field_order').value = str2;
	CurrentSubscriberList.submit();
}
function SearchData()
{
	document.getElementById('h_field_name').value = '';
	document.getElementById('h_field_order').value = '';
	CurrentSubscriberList.submit();
}
function CheckFavoriteData()
{	
	KEY = "CheckFavoriteData";			
	var s1 = "Usage History";		
	var s2 = "<?php echo $PageName; ?>";				
	makeRequest("ajax.php","REQUEST=FavoritesList&FeatureName=" +s1+"&PageName="+s2);
}
</script>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title> Coexsys Time Accounting </title>
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
    <!-- font-awasome-->
    <link href="css/font-awesome.min.css" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <!-- custom-css -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    
	<?php include("header.php"); ?>
	
    <section class="md-bg">
        <div class="container-fluid">
            <div class="row">
                <div class="brd-crmb">
                    <ul>
                        <li> <a href="#"> Subscriber Administration </a></li>
                        <li> <a href="#"> Usage History </a></li>
                    </ul>
                </div>
                <div class="dash-strip">
                    <div class="fleft cr-user">
                        <a href="index.php"> <button type="button" class="btn btn-primary dash"> Dashboard </button></a>
                    </div>
                    <div class="fright">
                        <?php
						$qry = "select * from cxs_users_favorites where USER_ID = $LoginUserId and PAGE_NAME ='$PageName'";
						$result=mysql_query	($qry);
						$TotalRecords = mysql_num_rows($result);
						if($TotalRecords == 0)
						{
							$s_Style = "";
						}
						else
						{
							$s_Style = "background-color: #000;";
						}
					?>	
						<button type="button" id = "cmdFavorites" name = "cmdFavorites" onclick = "CheckFavoriteData();" class="btn btn-warning fav-ico" style = "<?php echo $s_Style;?>"> <i class="fa fa-star"></i></button>
						<button type="button" class="btn btn-primary btn-style2" data-target="#FindUsageHistoryPopup" data-toggle="modal"> <i class="fa fa-search" aria-hidden="true"></i> Find Data</button>
						<button type="button" id="cmdRefresh" name="cmdRefresh"class="btn btn-primary btn-style2" ><i class="fa fa-refresh" aria-hidden="true"></i>Refresh</button>
                    </div>
                </div>
                <!-- search 
                <div class="search-bx">
                    <div class="cus-form-cont">
                        <div class="col-sm-3 form-group cus-form-ico">
                            <label> Start Date </label>
                            <input type="text" class="form-control" placeholder="Start Date">
                            <span class="inp-icons"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="col-sm-3 form-group cus-form-ico">
                            <label> End Date </label>
                            <input type="text" class="form-control" placeholder="End Date">
                            <span class="inp-icons"><i class="fa fa-calendar"></i></span>
                        </div>
                        <div class="col-sm-3 form-group">
                            <label> First Name </label>
                            <input type="text" class="form-control" placeholder="">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label> Last Name </label>
                            <input type="text" class="form-control" placeholder="">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label> User Name </label>
                            <input type="text" class="form-control" placeholder="">
                        </div>
                        <div class="col-sm-3 form-group">
                            <label> Supervisor </label>
                            <input type="text" class="form-control" placeholder="">
                        </div>
                        <div class="col-sm-3 form-group">

                            
                        </div>
                    </div>
                </div>
               search end -->

                <div class="cont-box">
                    <div class="pge-hd">
                        <h2 class="sec-title"> <label id="Label_Title"> Usage History </label> </h2>
                    </div>
                    <div>
                        <div class="fleft two">
                            <button type="button" class="btn-style btn"> Export </button>

                        </div>
						<form class="form" id="UsageHistoryList" name="UsageHistoryList" action="" method="POST">
                        <div class="data-bx">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th width="18%">
										<?php if($Sorts == 'desc' && $FileName == 'USER_NAME') { ?>
												<span style="">
													User Name
													<i style="cursor:pointer;" class="fa fa-sort pull-right" onclick="DataSort('USER_NAME','asc');"></i>
												</span>
										<?php } else { ?>
												<span style="">
													User Name
													<i style="cursor:pointer;" class="fa fa-sort pull-right" onclick="DataSort('USER_NAME','desc');"></i>
												</span>
										<?php } ?>
											</th>
											
											<th width="16%">
										<?php if($Sorts == 'desc' && $FileName == 'FULL_NAME') { ?>  
												<span style="">
													Full Name
													<i style="cursor:pointer;" class="fa fa-sort pull-right" onclick="DataSort('USER_NAME','asc');"></i>
												</span>
										<?php } else { ?>
												<span style="">
													Full Name
													<i style="cursor:pointer;" class="fa fa-sort pull-right" onclick="DataSort('USER_NAME','desc');"></i>
												</span>
										<?php } ?>
											</th>
											
											<th width="30%">
										<?php if($Sorts == 'desc' && $FileName == 'ACTIVITY_DATE') { ?>  
												<span style="">
													Activity Date
													<i style="cursor:pointer;" class="fa fa-sort pull-right" onclick="DataSort('USER_NAME','asc');"></i>
												</span>
										<?php } else { ?>
												<span style="">
													Activity Date
													<i style="cursor:pointer;" class="fa fa-sort pull-right" onclick="DataSort('USER_NAME','desc');"></i>
												</span>
										<?php } ?>
											</th>
                                            <th width="18%" > Supervisor </th>
                                            <th width="18%" > Activity Type </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
										$selectQuery = "SELECT  1  $SQueryOrderBy";
										$selectQueryForPages  = $selectQuery;
										$selectQuery = $selectQuery." limit $start_from , $record_per_page";
										//$RunUserQuery=mysql_query($selectQuery);
										//$StdNumRows = mysql_num_rows($RunUserQuery);
										$i= 1;
									
										//while($rows=mysql_fetch_array($RunUserQuery))
										{
											$UserId			= $rows['USER_ID'];
											$SubscriberName = $rows['USER_NAME'];
											$UserName 		= $rows['USER_NAMES'];
											$StartDate 		= $rows['END_DATE'];										
											$EndDate 		= $rows['END_DATE'];	
										
										?>
										<tr>
                                            <td> <?php echo "Bjohn"; ?> </td>
                                            <td> <?php echo "Bob"; ?> </td>
                                            <td> <?php echo "January 1st 2017,12:00 AM"; ?></td>
                                            <td> <?php echo "Steve"; ?> </td>
                                            <td> <?php echo "Time Entry"; ?> </td>
                                        </tr>
								<?php	
									$i=$i+1;
								} ?>
                                    </tbody>
                                </table>
                            </div>
							<input type="hidden" id="h_field_name" name="h_field_name" value="<?php echo $FileName; ?>">
							<input type="hidden" id="h_field_order" name="h_field_order" value="<?php echo $Sorts; ?>">
                        </div>
                        <!-- pagination start-->
                        <div class="pagination-bx">
                            <div class="bs-example">
                                <ul class="pagination">
									<?php
									//$RunDepQuery=mysql_query($selectQueryForPages);
									//$num_records = mysql_num_rows($RunDepQuery);
									//$total_pages= ceil($num_records/$record_per_page);
									
									if (($page-1)==0){ ?>
										<li class="disabled">
											<a rel="0" href="#">&laquo;</a>
										</li>
							<?php  	} else { ?>
										<li class="">
											<a rel="0" href="?type=<?php echo $GetType;?>&page=<?php echo ($page-1); ?>&sort=<?php echo $Sorts; ?>">&laquo;</a>
										</li>
							<?php 	}
									for($i=1;$i<=$total_pages;$i++){ ?>
										<li class="<?php echo ($page==$i)?'active':''; ?>"><a class="<?php echo ($page==$i)?'current':''; ?>" style = "<?php if($page==$i){echo 'background-color: #337ab7';} ?>" href="?type=<?php echo $GetType;?>&page=<?php echo $i;?>&sort=<?php echo $Sorts; ?>"><?php echo $i; ?></a></li>
							<?php 	}
									if (($page+1)>$total_pages){   ?>
										<li class="disabled"><a href="#">&raquo;</a></li>
							<?php  	} else { ?>
										<li class=""><a href="?type=<?php echo $GetType;?>&page=<?php echo ($page+1); ?>&sort=<?php echo $Sorts; ?>">&raquo;</a></li>
							<?php 	} ?>
									
                                </ul>
                            </div>
                        </div>
                        <!-- pagination end -->
						</form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>

    </footer>
<script type="text/javascript">
function makeRequest(url,data)
{
	var http_request = false;
	if (window.XMLHttpRequest) { // Mozilla, Safari, ...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			http_request.overrideMimeType('text/xml');
			// See note below about this line
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {}
		}
	}

	if (!http_request) {
		alert('Giving up :( Cannot create an XMLHTTP instance');
		return false;
	}
	http_request.onreadystatechange = function() { alertContents(http_request); };
	http_request.open('POST', url, true);
	http_request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	http_request.send(data);
}

function alertContents(http_request)
{
	if (http_request.readyState == 4)
	{
		if (http_request.status == 200)
		{ 
			if(KEY == "CheckFavoriteData")
			{
				var s1 = http_request.responseText;	
				s1=s1.trim();				
				str = s1;
				var n;
				n = str.lastIndexOf("No");					
				if (n>=0)//(s1=="No")
				{
					document.getElementById("cmdFavorites").style.backgroundColor = "#f0ad4e";
					s1 = str.substring(0,n);											
				}
				else
				{
					document.getElementById("cmdFavorites").style.backgroundColor = "#000";						
				}					
				document.getElementById("favorite_list").innerHTML = s1;
			}
		}
		else
		{
			document.getElementById(KEY).innerHTML = "";
			alert('There was a problem with the request.');
		}
	}
}	
	
</script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/custom.js" type="text/javascript"></script>
</body>

</html>