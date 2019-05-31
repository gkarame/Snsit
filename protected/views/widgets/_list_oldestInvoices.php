<div class="boardrow color333">
 		<!--  -->
			<div class="width154 inline-block">
		 		<span class="width154"><b>Customer</b></span>
		 	</div>
		 	<div class="width154 inline-block ">
		 		<span class="width154"><b>Invoice Number</b></span>
		 	</div>
		 	<div class="width154 inline-block">
		 		<span class="width154"><b>Amount $</b></span>
		 	</div>
		 	<div class="width154 inline-block">
		 		<span class="width154"><b>Age</b></span>
		 	</div>
		 	<div class="width154 inline-block">
		 		<span class="width154"><b>Resource</b></span>
		 	</div>
		 	<!--  -->
		</div>	
		<?//php $customers = WidgetOldestInvoices::getCustomers(); ?>
		<?php foreach ($customers as $customer) {
			?>
		<div class="boardrow odd-even default" >
		 <div class="boardrow odd-even default" >	
		 	<div class="width154 inline-block">
		 		<span  class="width154"><?php echo $customer['customer_name']; ?></span>
		 	</div>
		 		<div class="width154 inline-block">
		 		<span  class="width154"><?php echo $customer['Invoice_num']; ?></span>
		 	</div>
			
		 		<div class="width154 inline-block">
		 		<span  class="width154"><?php echo Utils::formatNumber($customer['amount']); ?></span>
		 	</div>
				<div class="width154 inline-block">
		 		<span  class="width154"><?php echo $customer['Age']; ?></span>
		 	</div>
		 	<div class="width154 inline-block">
		 		<span  class="width154"><?php echo $customer['Resource']; ?></span>
		 	</div>
		</div>	
		</div>
		<?php }?>