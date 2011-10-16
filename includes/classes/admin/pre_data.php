<?php
/**
 * Stores data
 *
 * @package Imagine Retailer
 * @since 1.0
 */
class Pre_Data {
	/**
	 * Returns all the sql for inserting pages
	 *
	 * @param int $website_id
	 * @return string
	 */
	public function pages_sql( $website_id ) {
		return str_replace( '[website_id]', sprintf( '%d', $website_id ), "INSERT INTO `website_pages` ( `website_id`, `slug`, `title`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `status`, `date_created` ) VALUES 
( [website_id], 'about-us', 'About Us', '&lt;h2&gt;&lt;img class=&quot;alignright&quot; title=&quot;Family shot&quot; src=&quot;http://www.concurringopinions.com/archives/images/family.jpg&quot; alt=&quot;&quot; width=&quot;189&quot; height=&quot;164&quot; style=&quot;float:right; padding-left:10px; padding-bottom:10px;&quot; /&gt;We&#039;ll Make Your House…A Home!&lt;/h2&gt; &lt;p&gt;ABC Home Furnishings family has been in business for over 30 years in Big Town, Louisiana. We originally started as Waterbed Sleep Shoppe and in 1988 we diversified our product line to carry a wide selection of bedroom, living room, and dining room furniture, in our beautifully decorated 33,000 square foot showroom.&lt;/p&gt; &lt;p&gt;We carry some of the most recognized names in furniture and mattresses: Ashley, Berkline, Broyhill, Coaster, and Sealy Mattresses.&lt;/p&gt; &lt;p&gt;Our family buyers continue to always search for the best buys and values in the furniture market. We shop during four international shows each year. Making certain to always find products coming from around the world. Today&#039;s fine furniture is built in The United States, Indonesia, South America, Canada, and China.&lt;/p&gt; &lt;p&gt;Count on us for:&lt;/p&gt; &lt;ul&gt; &lt;li&gt;Family service&lt;/li&gt; &lt;li&gt;Fast and friendly delivery&lt;/li&gt; &lt;li&gt;Great customer service&lt;/li&gt; &lt;li&gt;Knowledgeable and trained sales people&lt;/li&gt; &lt;li&gt;Guaranteed low prices on brand name furniture&lt;/li&gt; &lt;/ul&gt;', '', '', '', 1, NOW()),
( [website_id], 'contact-us', 'Contact Us', '&lt;p&gt;We love to hear from you! Please call, click or come on over.&lt;/p&gt;', '', '', '', 1, NOW()),
( [website_id], 'current-offer', 'Current Offer', '&lt;p&gt;Receive Exclusive Tips, Trends, Special Offers and Online Only Sales from ABC Furniture.&lt;/p&gt;', '', '', '', 1, NOW()),
( [website_id], 'financing', 'Financing', '&lt;p&gt;The &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; credit card gives you the flexibility to pay for your in-store purchases over time while you enjoy your new furniture now.&lt;/p&gt; &lt;h3&gt;&lt;a href=&quot;https://financial.wellsfargo.com/retailprivatelabel/entry.jsp&quot;&gt;Apply online for instant pre-approval before you shop!&lt;/a&gt;&lt;/h3&gt; &lt;p&gt;&lt;a href=&quot;https://financial.wellsfargo.com/retailprivatelabel/entry.jsp&quot; title=&quot;Apply Now&quot; target=&quot;_blank&quot;&gt;&lt;img src=&quot;/theme1/wp-content/uploads/2009/11/apply.gif&quot; alt=&quot;apply&quot; title=&quot;Apply Now&quot; width=&quot;146&quot; height=&quot;39&quot; /&gt;&lt;/a&gt;&lt;/p&gt; &lt;p&gt;As an &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; cardholder, you&#039;ll enjoy these benefits:&lt;/p&gt; &lt;ul&gt; &lt;li&gt;Convenient monthly payments&lt;/li&gt; &lt;li&gt;A revolving line of credit for your future furniture needs&lt;/li&gt; &lt;li&gt;Special promotional offers where available, including no-interest and reduced rate interest plans&lt;/li&gt; &lt;li&gt;No annual fee and no prepayment penalties&lt;/li&gt; &lt;li&gt;An easy-to-use online bill payment option&lt;/li&gt; &lt;/ul&gt; &lt;p&gt;The &lt;strong&gt;ABC Home Furnishings&lt;/strong&gt; credit card is provided by Wells Fargo Financial National Bank, a subsidiary of &lt;a title=&quot;Wells Fargo Financial&quot; href=&quot;http://financial.wellsfargo.com/&quot; target=&quot;_blank&quot;&gt;Wells Fargo Financial&lt;/a&gt;. Wells Fargo Financial is an affiliate of &lt;a title=&quot;Wells Fargo Bank, N.A&quot; href=&quot;http://www.wellsfargo.com/&quot; target=&quot;_blank&quot;&gt;Wells Fargo Bank, N.A&lt;/a&gt;&lt;/p&gt;', '', '', '', 1, NOW()),
( [website_id], 'home', 'Home', '&lt;p&gt;ABC Home Furnishings is family-owned and family-operated and has served Big Town, USA for over 30 years. &lt;a title=&quot;About Us&quot; href=&quot;http://furniture.imagineretailer.com/theme1/about-us/&quot;&gt;We have built our company by providing beautiful furniture, great service, low prices and hometown relationships from our family to yours.&lt;/a&gt;&lt;/p&gt; &lt;p&gt;ABC always offers simple to get, &lt;a title=&quot;Financing&quot; href=&quot;http://furniture.imagineretailer.com/theme1/financing/&quot;&gt;simple to use financing&lt;/a&gt;. Our programs often allow you to make payments while deferring interest, and always provide you benefits when shopping with us.&lt;/p&gt; &lt;p&gt;As a ABC Furniture cardholder, you&#039;ll enjoy benefits such as:&lt;br /&gt; &amp;bull; Convenient monthly payments&lt;br /&gt; &amp;bull; A revolving line of credit for all your purchasing needs&lt;br /&gt; &amp;bull; Special promotional offers where available, including no-interest and reduced rate interest plans&lt;br /&gt; &amp;bull; No annual fee and no prepayment penalties&lt;/p&gt; &lt;p&gt;Step inside our beautifully decorated showroom to browse a wide selection of&lt;a title=&quot;bedroom furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/bedrooms/&quot;&gt; bedroom,&lt;/a&gt; &lt;a title=&quot;living room furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/living-rooms/&quot;&gt;living room,&lt;/a&gt; &lt;a title=&quot;dining room furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/dining-rooms/&quot;&gt;and dining room furniture,&lt;/a&gt; &lt;a title=&quot;leather furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/leather/&quot;&gt;leather,&lt;/a&gt; &lt;a title=&quot;home office furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/home-office/&quot;&gt;home office,&lt;/a&gt; &lt;a title=&quot;kids furniture&quot; href=&quot;http://furniture.imagineretailer.com/theme1/c/furniture/youth/&quot;&gt;kids furniture &lt;/a&gt;and the area&#039;s largest selection of brand name mattresses and box spring sets. You&#039;ll find brands you recognize and trust including &lt;a title=&quot;Ashley Furniture&quot; href=&quot;http://www.ashleyfurniture.com/&quot;&gt;Ashley&lt;/a&gt;, &lt;a title=&quot;Berkline Furniture&quot; href=&quot;http://www.berkline.com/&quot;&gt;Berkline&lt;/a&gt;, &lt;a title=&quot;Broyhill Furniture&quot; href=&quot;http://www.broyhillfurniture.com/&quot;&gt;Broyhill&lt;/a&gt;, &lt;a title=&quot;Coaster Furniture&quot; href=&quot;http://coastercompany.com/&quot;&gt;Coaster&lt;/a&gt;, and &lt;a title=&quot;Sealy Bedding&quot; href=&quot;http://www.sealy.com/&quot;&gt;Sealy Mattresses&lt;/a&gt;.&lt;/p&gt; &lt;p&gt;Make your house a home at ABC Home Furnishings!&lt;/p&gt;', '', '', '', 1, NOW() ),
( [website_id], 'sidebar', 'Sidebar', '', '', '', '', 1, NOW()),
( [website_id], 'products', 'Products', '', '', '', '', 1, NOW()),
( [website_id], 'brands', 'Broducts', '', '', '', '', 1, NOW())" );
	}

	/**
	 * Returns all the sql for static attachments
	 *
	 * @param int $website_id
	 * @return string
	 */
	public function attachments_sql( $website_page_id ) {
		return str_replace( '[website_page_id]', sprintf( '%d', $website_page_id ), "INSERT INTO `website_attachments` ( `website_page_id`, `key`, `value`, `sequence`, `status` ) VALUES 
( [website_page_id], 'search', '', 1, 1 ),
( [website_page_id], 'video', '', 4, 1 ),
( [website_page_id], 'email', '', 5, 0 ),
( [website_page_id], 'room-planner', '', 6, 1 )");
	}

	/**
	 * Figure out what browser is used, its version and the platform it is running on.
	 *
	 * The following code was ported in part from JQuery v1.3.1
	 *
	 * @return array
	 */
	public function browser() {
		$user_agent = strtolower( $_SERVER['HTTP_USER_AGENT'] );

		// Identify the browser. Check Opera and Safari first in case of spoof. Let Google Chrome be identified as Safari.
		if( preg_match( '/opera/', $user_agent ) ) {
			$name = 'Opera';
		} elseif( preg_match( '/webkit/', $user_agent ) ) {
			$name = 'Safari';
		} elseif( preg_match( '/msie/', $user_agent ) ) {
			$name = 'Msie';
		} elseif( preg_match( '/mozilla/', $user_agent ) && !preg_match( '/compatible/', $user_agent ) ) {
			$name = 'Mozilla';
		} else {
			$name = 'unrecognized';
		}

		// What version?
		$version = ( preg_match( '/.+(?:firefox|it|ra|ie)[\/: ]?([\d.]+)/', $user_agent, $matches ) ) ? $matches[1] : 'unknown';

		// Running on what platform?
		if( preg_match('/linux/', $user_agent ) ) {
			$platform = 'Linux';
		} elseif( preg_match( '/macintosh|mac os x/', $user_agent ) ) {
			$platform = 'Mac';
		} elseif( preg_match( '/windows|win32/', $user_agent ) ) {
			$platform = 'Windows';
		} else {
			$platform = 'unrecognized';
		}

		$b = array(
			'name'	  		=> $name,
			'version'   	=> $version,
			'platform'  	=> $platform,
			'user_agent' 	=> $user_agent
		);
		
		return $b;
	}
}