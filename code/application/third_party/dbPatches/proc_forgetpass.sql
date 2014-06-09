DELIMITER $$

DROP PROCEDURE IF EXISTS `proc_forgetpass` $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `proc_forgetpass`(IN uactivationkey VARCHAR(50),IN md5pass VARCHAR(200))
BEGIN
SELECT * FROM useractivation where activationkey=uactivationkey;
IF Found_Rows() > 0 THEN
  update usermember set password=md5pass where id=(select memberid from useractivation where activationkey=uactivationkey);
  delete from useractivation where activationkey=uactivationkey;
END IF;

END $$

DELIMITER ;