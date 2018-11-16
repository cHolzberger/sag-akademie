<?php
/**
 */
class ViewNewsletterEmailTable extends Doctrine_Table
{
  function newsletterEntrys() {
      $q = Doctrine_Query::create()
  ->from('ViewNewsletterEmail email')
       ->where('email.email = ?')
       ->andWhere('email.newsletter = 1');
      return $q;
  }
}