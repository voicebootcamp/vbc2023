INSERT INTO `#__osmembership_configs` (`config_key`, `config_value`) VALUES
('activate_invoice_feature', '0'),
('send_invoice_to_customer', '0'),
('invoice_start_number', '1'),
('invoice_prefix', 'IV'),
('invoice_number_length', '5'),
('invoice_format', '<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\" width=\"100%\">\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td width=\"100%\">\r\n<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\" valign=\"top\" width=\"50%\">\r\n<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Company Name:</td>\r\n<td align=\"left\">Ossolution Team</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">URL:</td>\r\n<td align=\"left\">http://www.joomdonation.com</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Phone:</td>\r\n<td align=\"left\">84-972409994</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">E-mail:</td>\r\n<td align=\"left\">tuanpn@joomdonation.com</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Address:</td>\r\n<td align=\"left\">Lang Ha - Ba Dinh - Ha Noi</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td align=\"right\" valign=\"middle\" width=\"50%\"><img style=\"border: 0;\" src=\"media/com_osmembership/invoice_logo.png\" /></td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"2\" align=\"left\" width=\"100%\">\r\n<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\" valign=\"top\" width=\"50%\">\r\n<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td style=\"background-color: #d6d6d6;\" colspan=\"2\" align=\"left\">\r\n<h4 style=\"margin: 0px;\">Customer Information</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Name:</td>\r\n<td align=\"left\">[NAME]</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Company:</td>\r\n<td align=\"left\">[ORGANIZATION]</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Phone:</td>\r\n<td align=\"left\">[PHONE]</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Email:</td>\r\n<td align=\"left\">[EMAIL]</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Address:</td>\r\n<td align=\"left\">[ADDRESS], [CITY], [STATE], [COUNTRY]</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n<td align=\"left\" valign=\"top\" width=\"50%\">\r\n<table style=\"width: 100%;\" border=\"0\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td style=\"background-color: #d6d6d6;\" colspan=\"2\" align=\"left\">\r\n<h4 style=\"margin: 0px;\">Invoice Information</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Invoice Number:</td>\r\n<td align=\"left\">[INVOICE_NUMBER]</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Invoice Date:</td>\r\n<td align=\"left\">[INVOICE_DATE]</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\">Invoice Status:</td>\r\n<td align=\"left\">[INVOICE_STATUS]</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" width=\"50%\"> </td>\r\n<td align=\"left\"> </td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"background-color: #d6d6d6;\" colspan=\"2\" align=\"left\">\r\n<h4 style=\"margin: 0px;\">Order Items</h4>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"2\" align=\"left\" width=\"100%\">\r\n<table border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">\r\n<tbody>\r\n<tr>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">#</td>\r\n<td align=\"left\" valign=\"top\" width=\"60%\">Name</td>\r\n<td align=\"right\" valign=\"top\" width=\"20%\">Price</td>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">Sub Total</td>\r\n</tr>\r\n<tr>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">[ITEM_QUANTITY]</td>\r\n<td align=\"left\" valign=\"top\" width=\"60%\">[ITEM_NAME]</td>\r\n<td align=\"right\" valign=\"top\" width=\"20%\">[ITEM_AMOUNT]</td>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">[ITEM_SUB_TOTAL]</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" align=\"right\" valign=\"top\" width=\"90%\">Discount :</td>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">[DISCOUNT_AMOUNT]</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" align=\"right\" valign=\"top\" width=\"90%\">Subtotal :</td>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">[SUB_TOTAL]</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" align=\"right\" valign=\"top\" width=\"90%\">Tax :</td>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">[TAX_AMOUNT]</td>\r\n</tr>\r\n<tr>\r\n<td colspan=\"3\" align=\"right\" valign=\"top\" width=\"90%\">Total :</td>\r\n<td align=\"left\" valign=\"top\" width=\"10%\">[TOTAL_AMOUNT]</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>');