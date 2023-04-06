<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]>      <html class="no-js"> <!--<![endif]-->
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Budget logistics|Enquery Details</title>
        <meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <style>
 body{
    margin: 0;
    padding: 0;
    font: 400 .875rem 'Open Sans', sans-serif;
    color: #bcd0f7;
    background: #1A233A;
    position: relative;
    height: 100%;
}
.invoice-container {
    padding: 1rem;
}
.invoice-container .invoice-header .invoice-logo {
    margin: 0.8rem 0 0 0;
    display: inline-block;
    font-size: 1.6rem;
    font-weight: 700;
    color: #bcd0f7;
}
.invoice-container .invoice-header .invoice-logo img {
    max-width: 130px;
}
.invoice-container .invoice-header address {
    font-size: 0.8rem;
    color: #8a99b5;
    margin: 0;
}
.invoice-container .invoice-details {
    margin: 1rem 0 0 0;
    padding: 1rem;
    line-height: 180%;
    background: #1a233a;
}
.invoice-container .invoice-details .invoice-num {
    text-align: right;
    font-size: 0.8rem;
}
.invoice-container .invoice-body {
    padding: 1rem 0 0 0;
}
.invoice-container .invoice-footer {
    text-align: center;
    font-size: 0.7rem;
    margin: 5px 0 0 0;
}

.invoice-status {
    text-align: center;
    padding: 1rem;
    background: #272e48;
    -webkit-border-radius: 4px;
    -moz-border-radius: 4px;
    border-radius: 4px;
    margin-bottom: 1rem;
}
.invoice-status h2.status {
    margin: 0 0 0.8rem 0;
}
.invoice-status h5.status-title {
    margin: 0 0 0.8rem 0;
    color: #8a99b5;
}
.invoice-status p.status-type {
    margin: 0.5rem 0 0 0;
    padding: 0;
    line-height: 150%;
}
.invoice-status i {
    font-size: 1.5rem;
    margin: 0 0 1rem 0;
    display: inline-block;
    padding: 1rem;
    background: #1a233a;
    -webkit-border-radius: 50px;
    -moz-border-radius: 50px;
    border-radius: 50px;
}
.invoice-status .badge {
    text-transform: uppercase;
}

@media (max-width: 767px) {
    .invoice-container {
        padding: 1rem;
    }
}

.card {
    background: #272E48;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    border: 0;
    margin-bottom: 1rem;
}

.custom-table {
    border: 1px solid #2b3958;
}
.custom-table thead {
    background: #2f71c1;
}
.custom-table thead th {
    border: 0;
    color: #ffffff;
}
.custom-table > tbody tr:hover {
    background: #172033;
}
.custom-table > tbody tr:nth-of-type(even) {
    background-color: #1a243a;
}
.custom-table > tbody td {
    border: 1px solid #2e3d5f;
}

.table {
    background: #1a243a;
    color: #bcd0f7;
    font-size: .75rem;
}
.text-success {
    color: #c0d64a !important;
}
.custom-actions-btns {
    margin: auto;
    display: flex;
    justify-content: flex-end;
}
.custom-actions-btns .btn {
    margin: .3rem 0 .3rem .3rem;
}

        </style>
    </head>
    <body>
    <div class="container">
    <div class="row gutters" style="align-items: center;
    justify-content: center;
    display: flex;
    height: 100vh;">
    	<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
    		<div class="card">
    			<div class="card-body p-0">
    				<div class="invoice-container">
    					<div class="invoice-header">
    
    					
    
    						<!-- Row start -->
    						<div class="row gutters">
    							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6">
    								<a href="https://shopninja.in/budgetlog" class="invoice-logo">
    									Budget Logistics
    								</a>
    							</div>
    							<div class="col-lg-6 col-md-6 col-sm-6">
    								<address class="text-right" style="text-align:right;">
    									Name : <span style="text-transform:uppercase">{{$enquery->user->name}}</span><br><br>
    									Email: {{$enquery->user->email}}<br><br>
    									Phone: {{$enquery->user->mobile}}
    								</address>
    							</div>
    						</div>
    						<!-- Row end -->
    
    						<!-- Row start -->
    						<div class="row gutters">
    							<div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
    								<div class="invoice-details">
                                        <strong><h5>From</h5></strong>
    									<address>
    										Location: {{$enquery->from_floor}} (Floor), {{$enquery->from_location}}<br>
                                            Lift Available: {{ $enquery->from_lift == 0 ? 'NO' : 'YES' }}
    									</address>
    								</div>
    							</div>
                                <div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
    								<div class="invoice-details">
                                        <strong><h5>To</h5></strong>
    									<address>
                                        Location: {{$enquery->to_floor}} (Floor), {{$enquery->to_location}}<br>
                                        Lift Available: {{ $enquery->to_lift == 0 ? 'NO' : 'YES' }}
    									</address>
    								</div>
    							</div>
    							<div class="col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12">
    								<div class="invoice-details">
    									<div class="invoice-num">
                                            <div>Moving {{$enquery->from_bhk}} BHK</div>
    										<div>Enquery Id - {{$enquery->id}}</div>
    										<div>{{$enquery->date}}</div>
    									</div>
    								</div>													
    							</div>
    						</div>
    						<!-- Row end -->
    
    					</div>
    
    					<div class="invoice-body">
    
    						<!-- Row start -->
    						<div class="row gutters">
    							<div class="col-lg-12 col-md-12 col-sm-12">
    								<div class="table-responsive">
										<div class="row  gx-0 justify-content-center">
										@foreach($products as $key => $product)
										<div class="col-sm-6 col-lg-3">
										<h2 style="text-transform:uppercase">{{$key}}</h2>
    									<table class="table custom-table m-0">
    										<thead style="width:100%;">
    											<tr style="width:100%;">
													
    												<th style="width:50%;">Attribute</th>
    												<th style="width:50%;">Value</th>
    												
    											</tr>
    										</thead>
    										<tbody style="width:100%;">
										
											@foreach($product as $attribute)
    											<tr style="width:100%;">
											
												  
    												<td style="width:50%; text-transform:uppercase">
    													{{$attribute['attribute_name']}}
    													
    												</td>
    												<td style="width:50%; text-transform:uppercase">{{$attribute['attribute_value']}}</td>
    											
												</tr>
											@endforeach
    									
                                            
    										</tbody>
										</table>
										</div>
										@endforeach	
                                        </div>
										<table>
											<tbody>
										
    											<tr>
    												<td>&nbsp;</td>
    												<td colspan="2">
    													
    													<h3 class="text-success text-uppercase"><strong>Estimation:</strong></h3>
    												</td>			
    												<td>
    													
    													<h3 class="text-success"><strong>&nbsp;₹{{$enquery->price}}</strong></h3>
    												</td>
    											</tr>
                                          
											</tbody>
										</table>
    								</div>
    							</div>
    						</div>
    						<!-- Row end -->
    
    					</div>
    
    					<div class="invoice-footer">
    						Thank you for your Enquery.
    					</div>
    
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
</div>
        
     
    </body>
</html>

