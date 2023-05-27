(function ($, products, upsells, is_product_page, ajax_object, Modal) {
	/* Remove blue success banner */
	if (is_product_page) {
		var element = document.createElement("style"),
			sheet;
		document.head.appendChild(element);
		sheet = element.sheet;
		let cs = "#genesis-content > div.woocommerce-notices-wrapper { display: none; }";
		sheet.insertRule(cs, 0);
	}

	/* UI, open pop-up bootstrap */
	function openModal(products, upsells) {
		var myvar =
			"<!-- Main modal -->" +
			'<div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">' +
			'    <div class="relative w-full max-w-2xl max-h-full">' +
			"        <!-- Modal content -->" +
			'        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">' +
			"            <!-- Modal header -->" +
			'            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">' +
			'                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">' +
			"                    Product added to cart!" +
			"                </h3>" +
			'                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="defaultModal">' +
			'                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>' +
			'                    <span class="sr-only">Close modal</span>' +
			"                </button>" +
			"            </div>" +
			"            <!-- Modal body -->" +
			'            <div class="p-6 space-y-6">' +
			'                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">' +
			"                    With less than a month to go before the European Union enacts new consumer privacy laws for its citizens, companies around the world are updating their terms of service agreements to comply." +
			"                </p>" +
			'                <p class="text-base leading-relaxed text-gray-500 dark:text-gray-400">' +
			"                    The European Unionâ€™s General Data Protection Regulation (G.D.P.R.) goes into effect on May 25 and is meant to ensure a common set of data rights in the European Union. It requires organizations to notify users as soon as possible of high-risk data breaches that could personally affect them." +
			"                </p>" +
			"            </div>" +
			"            <!-- Modal footer -->" +
			'            <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">' +
			'                <button data-modal-hide="defaultModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">I accept</button>' +
			'                <button data-modal-hide="defaultModal" type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">Decline</button>' +
			"            </div>" +
			"        </div>" +
			"    </div>" +
			"</div>";

		//$("body").append(myvar);

		// var body =
		// 	'<div style="padding-left: 20px; padding-right: 20px;">' +
		// 	'<div class="row" style="clear:both; margin-bottom:20px; margin-top: 20px;">' +
		// 	'<div class="col-md-12" style="padding: 0 !important;">' +
		// 	'<h4 class="winkelwagen-popup-h4">Toegevoegd aan je winkelmand</h4>' +
		// 	'<button class="winkelwagen-popup-buttonverderwinkelen" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"> <span>Verder winkelen</span></i></button>' +
		// 	"</div>" +
		// 	"</div>";

		// product = products.shift();

		// if (!product) return;

		// body +=
		// 	'<div class="row" style="display: flex; justify-content: center; clear:both; border-bottom: 1px solid #e6e6e6 !important; border-top: 1px solid #e6e6e6 !important; margin-bottom: 20px; padding-top: 10px; padding-bottom: 10px;">' +
		// 	'<div class="col-lg-2 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' +
		// 	'<img src="' +
		// 	product["image"] +
		// 	'" width="100px"></img>' +
		// 	"</div>" +
		// 	'<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: flex-start;">' +
		// 	'<span class="winkelwagen-popup-name">' +
		// 	product["name"] +
		// 	"</span>" +
		// 	"</div>" +
		// 	'<div class="col-lg-1 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' +
		// 	'<span class="winkelwagen-popup-name">x' +
		// 	product["quantity"] +
		// 	"</span>" +
		// 	"</div>" +
		// 	'<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center; flex-direction: column;">' +
		// 	'<div class="winkelwagen-popup-price ex-btw">€ ' +
		// 	Number(product["price"] * product["quantity"]).toLocaleString("nl-BE", {
		// 		minimumFractionDigits: 2,
		// 		maximumFractionDigits: 2,
		// 	}) +
		// 	" <span>Excl. btw</span></div>" +
		// 	'<div class="winkelwagen-popup-price inc-btw">€ ' +
		// 	Number(product["price"] * product["quantity"] * 1.21).toLocaleString("nl-BE", {
		// 		minimumFractionDigits: 2,
		// 		maximumFractionDigits: 2,
		// 	}) +
		// 	" <span>Incl. btw</span></div>" +
		// 	"</div>" +
		// 	'<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' +
		// 	'<button class="winkelwagen-popup-button"><a id="winkelwagenpopup-button-link" href="/winkelmand">Winkelmand</a><i class="fa fa-arrow-right"></i></button>' +
		// 	"</div>" +
		// 	"</div>";

		// products.forEach((product) => {
		// 	body +=
		// 		'<div class="row" style="display: flex; justify-content: center; clear:both; border-bottom: 1px solid #e6e6e6 !important; border-top: 1px solid #e6e6e6 !important; margin-bottom: 20px; padding-top: 10px; padding-bottom: 10px;">' +
		// 		'<div class="col-lg-2 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' +
		// 		'<img src="' +
		// 		product["image"] +
		// 		'" width="100px"></img>' +
		// 		"</div>" +
		// 		'<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: flex-start;">' +
		// 		'<span class="winkelwagen-popup-name">' +
		// 		product["name"] +
		// 		"</span>" +
		// 		"</div>" +
		// 		'<div class="col-lg-1 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' +
		// 		'<span class="winkelwagen-popup-name">x' +
		// 		product["quantity"] +
		// 		"</span>" +
		// 		"</div>" +
		// 		'<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center; flex-direction: column;">' +
		// 		'<div class="winkelwagen-popup-price ex-btw">€ ' +
		// 		Number(product["price"] * product["quantity"]).toLocaleString("nl-BE", {
		// 			minimumFractionDigits: 2,
		// 			maximumFractionDigits: 2,
		// 		}) +
		// 		" <span>Excl. btw</span></div>" +
		// 		'<div class="winkelwagen-popup-price inc-btw">€ ' +
		// 		Number(product["price"] * product["quantity"] * 1.21).toLocaleString("nl-BE", {
		// 			minimumFractionDigits: 2,
		// 			maximumFractionDigits: 2,
		// 		}) +
		// 		" <span>Incl. btw</span></div>" +
		// 		"</div>" +
		// 		'<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' +
		// 		"</div>" +
		// 		"</div>";
		// });
		// if (upsells && upsells.length > 0) {
		// 	body +=
		// 		'<div class="row" style="margin-bottom: 20px;"><div class="col-md-12" style="text-align:center;"><h5 class="winkelwagen-popup-h5">Ook handig om gelijk mee te bestellen!</h5></div></div>' +
		// 		'<div class="products row" style="align-items: center; justify-content: center;">';
		// 	upsells.forEach((upsell) => {
		// 		body +=
		// 			'<div class="row" style="clear:both; border-bottom: 1px solid #e6e6e6 !important; border-top: 1px solid #e6e6e6 !important; margin-bottom: 20px; padding-top: 10px; padding-bottom: 10px;">' +
		// 			'<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' +
		// 			'<img src="' +
		// 			upsell["image"] +
		// 			'" width="100px"></img>' +
		// 			"</div>" +
		// 			'<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' +
		// 			'<span class="winkelwagen-popup-name"><a href="' +
		// 			upsell["link"] +
		// 			'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">' +
		// 			upsell["name"] +
		// 			"</a></span>" +
		// 			"</div>" +
		// 			'<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center; flex-direction: column;">' +
		// 			'<div class="winkelwagen-popup-price ex-btw">€ ' +
		// 			Number(upsell["price"]).toLocaleString("nl-BE", { minimumFractionDigits: 2, maximumFractionDigits: 2 }) +
		// 			" <span>Excl. btw</span></div>" +
		// 			'<div class="winkelwagen-popup-price inc-btw">€ ' +
		// 			Number(upsell["price"] * 1.21).toLocaleString("nl-BE", {
		// 				minimumFractionDigits: 2,
		// 				maximumFractionDigits: 2,
		// 			}) +
		// 			" <span>Incl. btw</span></div>" +
		// 			"</div>" +
		// 			'<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' +
		// 			'<button class="winkelwagen-popup-button"><a href="?add-to-cart=' +
		// 			upsell["id"] +
		// 			'" data-open="0" data-quantity="1" class="button wp-element-button product_type_simple ajax_add_to_cart add_to_cart_button" data-product_id="' +
		// 			upsell["id"] +
		// 			'" data-product_sku="" aria-label="Toevoegen" rel="nofollow">Toevoegen</a></button>' +
		// 			"</div>" +
		// 			"</div>";
		// 	});
		// 	body += "</div>";
		// }
		// bootstrap.showModal({
		// 	title: "winkelmand",
		// 	backdrop: true,
		// 	body,
		// 	footer: '<h1 class="text-3xl font-bold underline">Hello world!</h1>',
		// 	onCreate: function (modal) {},
		// });

		// set the modal menu element
		const $targetEl = document.getElementById("defaultModal");

		// options with default values
		const options = {
			placement: "bottom-right",
			backdrop: "dynamic",
			backdropClasses: "bg-gray-900 bg-opacity-50 dark:bg-opacity-80 fixed inset-0 z-40",
			closable: true,
			onHide: () => {
				console.log("modal is hidden");
			},
			onShow: () => {
				console.log("modal is shown");
			},
			onToggle: () => {
				console.log("modal has been toggled");
			},
		};

		const modal = new Modal($targetEl, options);
		modal.show();
	}

	/* Triggers */
	/* After DOM is ready */
	$(document).ready(function () {
		/* Open pop-up if blue banner appears in dom */
		//if ($(".woocommerce-message > a").html() == "Winkelwagen bekijken") {
		openModal(products, upsells);
		$(".modal-dialog").addClass("modal-lg");
		$(".modal-header").remove();
		//$(".modal-footer").remove();
		//}

		$("body").on("hidden.bs.modal", ".modal", function () {
			$.removeCookie("itemsincart", { path: "/" });
			$.removeCookie("newtocart", { path: "/" });
			$.removeCookie("itemsincart", { path: "tankkopenbe.boltestnl.be" });
			$.removeCookie("newtocart", { path: "tankkopenbe.boltestnl.be" });
		});

		$("body").on("click", ".modal", function (ev) {
			if (ev.target != $(this)[0]) return;
			$(".modal").modal("hide");
		});

		/* Open pop-up on category page */
		$(".ajax_add_to_cart").click(function (ev) {
			if ($(this).data("open") != undefined) return;

			$.ajax({
				url: ajax_object.ajaxurl,
				type: "POST",
				data: {
					action: "get_productinfo",
					product_id: $(this).data("product_id"),
				},
				success: function (data) {
					data = JSON.parse(data);
					var product = {
						id: $(this).data("product_id"),
						image: data["image"],
						price: data["price"],
						name: data["name"],
						quantity: 1,
					};
					openModal([product], data["upsells"]);
					$(".modal-dialog").addClass("modal-lg");
					$(".modal-header").remove();
					$(".modal-footer").remove();
				},
			});
		});
	});
})(jQuery, products, upsells, is_product_page, ajax_object, Modal);
