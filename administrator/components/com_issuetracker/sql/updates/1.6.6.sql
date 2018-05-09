ALTER TABLE `#__it_people` DROP INDEX `#__it_people_phone_number_uk`;
ALTER TABLE `#__it_people`
  ADD INDEX `#__it_people_phone_number_uk` (phone_number);