<?php

/*
#########################################################################
#  Copyright (c) 2005-2006. Punch Software. All Rights Reserved.
#
#  Punch software [both binary and source (if released)] (hereafter,
#  Software) is intellectual property owned by Punch Software and
#  phixel.org and is copyright of Punch Software and phixel.org in all
#  countries in the world, and ownership remains with Punch Software and
#  phixel.org.
#
#  You (hereafter, Licensee) are not allowed to distribute the binary and
#  source code (if released) to third parties. Licensee is not allowed to
#  reverse engineer, disassemble or decompile code, or make any
#  modifications of the binary or source code, remove or alter any
#  trademark, logo, copyright or other proprietary notices, legends,
#  symbols, or labels in the Software.
#
#  Licensee is not allowed to sub-license the Software or any derivative
#  work based on or derived from the Software.
#
#  The Licensee acknowledges and agrees that the software is delivered
#  'as is' without warranty and without any support services (unless
#  agreed otherwise with Punch Software or phixel.org). Punch Software
#  and phixel.org make no warranties, either expressed or implied, as to
#  the software and its derivatives.
#
#  It is understood by Licensee that neither Punch Software nor
#  phixel.org shall be liable for any loss or damage that may arise,
#  including any indirect special or consequential loss or damage in
#  connection with or arising from the performance or use of the
#  software, including fitness for any particular purpose.
#
#  By using or copying this Software, Licensee agrees to abide by the
#  copyright law and all other applicable laws of The Netherlands
#  including, but not limited to, export control laws, and the terms of
#  this licence. Punch Software and/or phixel.org shall have the right to
#  terminate this licence immediately by written notice upon Licensee's
#  breach of, or non-compliance with, any of its terms. Licensee may be
#  held legally responsible for any copyright infringement that is caused
#  or encouraged by Licensee's failure to abide by the terms of this
#  licence.
#########################################################################
*/

/*
 * Product Class v0.1.1
 * Retrieves product data from the database.
 */

class Product extends DBA_Product {

	public static function getProducts() {
		/**
		*  @desc: Get all products.
		*  @return: Collection of AccountProduct objects .
		*  @type: public static
		*/

		$strSql = sprintf("SELECT * FROM punch_product WHERE parentId = '0'");
		return self::select($strSql);
	}

}

?>