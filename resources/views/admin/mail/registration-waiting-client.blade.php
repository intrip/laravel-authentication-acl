<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<!-- If you delete this meta tag, Half Life 3 will never be released. -->
<meta name="viewport" content="width=device-width" />

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>{!! Config::get('acl_base.app_name') !!}</title>
{!! HTML::style('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css') !!}
{!! HTML::style('css/email.css') !!}

</head>
 
<body bgcolor="#FFFFFF" style="max-width: 676px;">

<!-- HEADER -->
<table class="head-wrap">
	<tr>
		<td></td>
		<td class="header container" >
				
				<div class="content">
				<table>
					<tr>
						<td><img src="{{ asset('/img/logo-black.png') }}" alt="ErandPlace"/></td>
					</tr>
				</table>
				</div>
				
		</td>
		<td></td>
	</tr>
</table><!-- /HEADER -->
<!-- BODY -->
<table class="body-wrap">
	<tr>
		<td></td>
		<td class="container" bgcolor="#FFFFFF">

			<div class="content">
			<table>
				<tr>
					<td>
<!--                        <h2><i class="fa fa-pencil"></i> Registration request on: {!!Config::get('acl_base.app_name')!!}</h2>-->

						<h3>Dear {!! $body['first_name'] !!},</h3>
						
						<!-- Callout Panel -->
						<p class="callout" style="background-color: #ccc; padding: 10px;">
							<strong>Your account has been created. However, before you can use it you need to confirm your email address first by clicking </strong> 
                           <a href="{!!URL::route('user.email-confirmation', ['token' => $body['token'], 'email' => $body['email'] ] )!!}" style="color:#15c;">this link</a>
						</p><!-- /Callout Panel -->					
						<p  style="background-color: #ccc;">
                             <strong>Please find your account details below: </strong>
                                <ul>
                                    <li>Username: {!! $body['email']!!}</li>
                                    <li>Password: {!! $body['password']!!}</li>
                                </ul>
                        </p>

						<!-- social & contact -->
						<table class="social" width="100%">
							<tr>
								<td>
									
									 
									<!--<table align="left" class="column">
										<tr>
											<td>				
												
												<h5 class="">Connect with Us:</h5>
												<p class="">
													<a href="#" class="soc-btn fb">Facebook</a> 
													<a href="#" class="soc-btn tw">Twitter</a> 
													<a href="#" class="soc-btn gp">Google+</a></p>
						
												
											</td>
										</tr>
									</table> /column 1 	-->
									
									<!-- column 2 -->
									<table align="left" class="column">
										<tr>
											<td>				
																			
												<h5 class="">Contact Info:</h5>												
												<p>Phone: <strong>+234 01 291 7303</strong><br/>
                            Email: <strong><a href="emailto:hello@errandplace.com">hello@errandplace.com</a></strong></p>
                
											</td>
										</tr>
									</table><!-- /column 2 -->
									
									<span class="clear"></span>	
									
								</td>
							</tr>
						</table><!-- /social & contact -->
						
					</td>
				</tr>
			</table>
			</div><!-- /content -->
									
		</td>
		<td></td>
	</tr>
</table><!-- /BODY -->

<!-- FOOTER
<table class="footer-wrap">
	<tr>
		<td></td>
		<td class="container">
			
				
				<div class="content">
				<table>
				<tr>
					<td align="center">
						<p>
							<a href="#">Terms</a> |
							<a href="#">Privacy</a> |
							<a href="#"><unsubscribe>Unsubscribe</unsubscribe></a>
						</p>
					</td>
				</tr>
			</table>
				</div>
				
		</td>
		<td></td>
	</tr>
</table> /FOOTER -->

</body>
</html>