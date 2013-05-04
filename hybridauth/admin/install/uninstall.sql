-- -----------------------------------------------------
-- HOW TO USE THIS FILE:
-- Replace all instances of #_ with your prefix
-- In PHPMYADMIN or the equiv, run the entire SQL
-- -----------------------------------------------------

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

drop table if exists `#__hybridauth_addresses`;
drop table if exists `#__hybridauth_carts`;
drop table if exists `#__hybridauth_categories`;
drop table if exists `#__hybridauth_config`;
drop table if exists `#__hybridauth_countries`;
drop table if exists `#__hybridauth_currencies`;
drop table if exists `#__hybridauth_geozones`;
drop table if exists `#__hybridauth_geozonetypes`;
drop table if exists `#__hybridauth_manufacturers`;
drop table if exists `#__hybridauth_ordercoupons`;
drop table if exists `#__hybridauth_orderhistory`;
drop table if exists `#__hybridauth_orderinfo`;
drop table if exists `#__hybridauth_orderitems`;
drop table if exists `#__hybridauth_orderitemattributes`;
drop table if exists `#__hybridauth_orderpayments`;
drop table if exists `#__hybridauth_orders`;
drop table if exists `#__hybridauth_ordershippings`;
drop table if exists `#__hybridauth_orderstates`;
drop table if exists `#__hybridauth_ordertaxclasses`;
drop table if exists `#__hybridauth_ordertaxrates`;
drop table if exists `#__hybridauth_ordervendors`;
drop table if exists `#__hybridauth_productattributeoptions`;
drop table if exists `#__hybridauth_productattributes`;
drop table if exists `#__hybridauth_productcategoryxref`;
drop table if exists `#__hybridauth_productcomments`;
drop table if exists `#__hybridauth_productcommentshelpfulness`;
drop table if exists `#__hybridauth_productdownloadlogs`;
drop table if exists `#__hybridauth_productdownloads`;
drop table if exists `#__hybridauth_productfiles`;
drop table if exists `#__hybridauth_productprices`;
drop table if exists `#__hybridauth_productquantities`;
drop table if exists `#__hybridauth_productrelations`;
drop table if exists `#__hybridauth_productreviews`;
drop table if exists `#__hybridauth_products`;
drop table if exists `#__hybridauth_productvotes`;
drop table if exists `#__hybridauth_shippingmethods`;
drop table if exists `#__hybridauth_shippingrates`;
drop table if exists `#__hybridauth_subscriptions`;
drop table if exists `#__hybridauth_subscriptionhistory`;
drop table if exists `#__hybridauth_taxclasses`;
drop table if exists `#__hybridauth_taxrates`;
drop table if exists `#__hybridauth_userinfo`;
drop table if exists `#__hybridauth_zonerelations`;
drop table if exists `#__hybridauth_zones`;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;