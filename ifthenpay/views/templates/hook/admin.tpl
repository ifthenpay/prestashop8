{*
* 2007-2023 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2023 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<div class="card mt-2" id="view_order_payments_block">
	<div class="card-header">
		<h3 class="card-header-title">
		Ifthenpay
		</h3>
	</div>

	<div class="card-body">

		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-order-ifthenpay">
					<div class="panel-heading">
						<h3>{l s='Pay by %s' mod='ifthenpay' sprintf=[$paymentMethod|ucfirst]}</h3>
					</div>
					<div>{$message}</div>
					<div class="panel-body">
						<div class="row m_bottom_20">
							<div class="paymentLogo  col-auto">
								<img id="pm_logo" src="{$paymentLogo}">
							</div>
							<div class="paymentData  col">
								{if $paymentMethod == 'multibanco'}
									<ul class="list-group">
										<li class="list-group-item">
											{l s='Entity:' mod='ifthenpay'}
											<span class="badge">{$entidade}</span>
										</li>
										<li class="list-group-item">
											{l s='Reference:' mod='ifthenpay'}
											<span class="badge">{$referencia}</span>
										</li>
										{if $validade != ''}
											<li class="list-group-item">
												{l s='Deadline:' mod='ifthenpay'}
												<span class="badge">{$validade}</span>
											</li>
										{/if}
										<li class="list-group-item">
											{l s='Total to Pay:' mod='ifthenpay'}
											<span class="badge">{$totalToPay}</span>
										</li>
									</ul>
								{elseif $paymentMethod == 'mbway'}
									<ul class="list-group">
										<li class="list-group-item">
											{l s='Phone:' mod='ifthenpay'}
											<span class="badge">{$telemovel}</span>
										</li>
										<li class="list-group-item">
											{l s='MB WAY Request ID:' mod='ifthenpay'}
											<span class="badge">{$idPedido}</span>
										</li>
										<li class="list-group-item">
											{l s='Total to Pay:' mod='ifthenpay'}
											<span class="badge">{$totalToPay}</span>
										</li>
									</ul>
								{elseif $paymentMethod == 'payshop'}
									<ul class="list-group">
										<li class="list-group-item">
											{l s='Reference:' mod='ifthenpay'}
											<span class="badge">{$referencia}</span>
										</li>
										{if $validade != ''}
											<li class="list-group-item">
												{l s='Deadline:' mod='ifthenpay'}
												<span class="badge">{$validade}</span>
											</li>
										{/if}
										<li class="list-group-item">
											{l s='IdRequest:' mod='ifthenpay'}
											<span class="badge">{$idPedido}</span>
										</li>
										<li class="list-group-item">
											{l s='Total to Pay:' mod='ifthenpay'}
											<span class="badge">{$totalToPay}</span>
										</li>
									</ul>
								{else}
									<ul class="list-group">
										<li class="list-group-item">
											{l s='IdRequest:' mod='ifthenpay'}
											<span class="badge">{$idPedido}</span>
										</li>
										<li class="list-group-item">
											{l s='Total to Pay:' mod='ifthenpay'}
											<span class="badge">{$totalToPay}</span>
										</li>
									</ul>
								{/if}
							</div>
						</div>

						<div class="row">
							{if $paymentMethod == 'multibanco' || $paymentMethod == 'payshop' || $paymentMethod == 'mbway'}
								<div class="adm_hist_actions">
									<a href="{$updateControllerUrl}"
										class=" btn btn-primary">{l s='Update %s Data' mod='ifthenpay' sprintf=[$paymentMethod|ucfirst]}</a>
								</div>
							{/if}
							{if $paymentMethod == 'mbway' && $idPedido && $telemovel}
								<div class="adm_hist_actions">
									<a href="{$resendControllerUrl}"
										class=" btn btn-primary">{l s='Resend Payment Data' mod='ifthenpay' }</a>
								</div>
							{elseif $paymentMethod == 'mbway'}
								<div class="adm_hist_actions">
									<a id="resendPaymentBtn" href="{$resendControllerUrl}"
										class="btn btn-primary">{l s='Resend Payment Data' mod='ifthenpay' }</a>
								</div>
							{/if}
							{if $paymentMethod == 'multibanco' || $paymentMethod == 'payshop'}
								<div class="adm_hist_actions">
									<a href="{$resendControllerUrl}"
										class=" btn btn-primary">{l s='Resend Payment Data' mod='ifthenpay' }</a>
								</div>
								<div class="adm_hist_actions">
									<a href="{$rememberControllerUrl}"
										class=" btn btn-primary">{l s='Remember Payment Details' mod='ifthenpay' }</a>
								</div>
							{/if}
							<div class="adm_hist_actions new_payment">
								<a id="chooseNewPaymentMethod" href="{$chooseNewPaymentMethodControllerUrl}"
									class=" btn btn-primary">{l s='Choose new Payment Method' mod='ifthenpay'}</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
  <script>
    {
      * PARTIAL REFUND METHOD *
    }
    $(document).ready(function() {
          {
            if $swtichRefund
          }
          const refundBtn = document.getElementsByClassName('partial-refund-display');
          let paymentsList = []; {
            foreach $paymentMethods as $key => $value
          }
          paymentsList.push('{$value}'); {
            /foreach} {
              /if}
              if (refundBtn.length === 1) {
                refundBtn[0].addEventListener("click", function(event) {
                    if (paymentsList.includes('{$paymentMethod}')) {
                      document.getElementById("cancel_product_save").disabled = true;
                      const msgbox = document.getElementById("content-message-box"); {
                        if $paymentMethod == 'mbway' || $paymentMethod == 'ccard'
                      }
                      let confirmRefund = confirm('{$confirmRefund}');
                      if (confirmRefund) {
                        $.ajax({
                          url: '{$refundControllerUrl}',
                          type: 'GET',
                          headers: {
                            "cache-control": "no-cache"
                          },
                          success: function(data) {
                            const initialDate = Date.now();
                            const timeLimit = 10 * 60 * 1000;
                            let msg = document.createElement("div");
                            msg.innerHTML = " < div class = 'alert alert-success' > {
                              $refundNotification
                            } < /div>";
                            msgbox.appendChild(msg);
                            setTimeout(function() {
                              let securityCode = prompt("{$promptCode}");
                              if (securityCode == JSON.parse(data).code) {
                                if (Date.now() - initialDate > timeLimit) {
                                  let msg = document.createElement("div");
                                  msg.innerHTML = " < div class = 'alert alert-danger' > " + " {
                                    $invalidCode
                                  }
                                  " + " {
                                    $timeExceeded
                                  }
                                  " + " < /div>";
                                  msgbox.appendChild(msg);
                                } else {
                                  document.getElementById("cancel_product_save").disabled = false;
                                  let msg = document.createElement("div");
                                  msg.innerHTML = " < div class = 'alert alert-success' > {
                                    $validationSuccessful
                                  } < /div>";
                                  msgbox.appendChild(msg);
                                }
                              } else {
                                let msg = document.createElement("div");
                                msg.innerHTML = " < div class = 'alert alert-danger' > " + " {
                                  $invalidCode
                                }
                                " + "!" + " < /div>";
                                msgbox.appendChild(msg);
                              }
                            }, 500)
                          },
                          error: function(jqXHR, textStatus, errorThrown) {
                            alert('Ocorreu um erro: ' + textStatus + ', ' + errorThrown);
                          }
                        });
                      } {
                        else
                      }
                      let msg = document.createElement("div");
                      msg.innerHTML = " < div class = 'alert alert-warning' > " + " {
                        $refundNotAvailable
                      }
                      " + " {
                        $paymentMethod
                      }
                      " + " < /div>";
                      msgbox.appendChild(msg); {
                        /if}
                      }
                    });
                }
              });
  </script>