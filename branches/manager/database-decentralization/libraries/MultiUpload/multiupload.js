/**
 * Convert a single file-input element into a 'multiple' input list
 *
 * Usage:
 *
 *   1. Create a file input element (no name)
 *      eg. <input type="file" id="first_file_element">
 *
 *   2. Create a DIV for the output to be written to
 *      eg. <div id="files_list"></div>
 *
 *   3. Instantiate a MultiSelector object, passing in the DIV and an (optional) maximum number of files
 *      eg. var multi_selector = new MultiSelector(document.getElementById('files_list'), 3);
 *
 *   4. Add the first element
 *      eg. multi_selector.addElement(document.getElementById('first_file_element'));
 *
 *   5. That's it.
 *
 *   You might (will) want to play around with the addListRow() method to make the output prettier.
 *
 *   You might also want to change the line 
 *       element.name = 'file_' + this.count;
 *   ...to a naming convention that makes more sense to you.
 * 
 * Licence:
 *   Use this however/wherever you like, just don't blame me if it breaks anything.
 *
 * Credit:
 *   If you're nice, you'll leave this bit:
 *  
 *   Class by Stickman -- http://www.the-stickman.com
 *      with thanks to:
 *      [for Safari fixes]
 *         Luis Torrefranca -- http://www.law.pitt.edu
 *         and
 *         Shawn Parker & John Pennypacker -- http://www.fuzzycoconut.com
 *      [for duplicate name bug]
 *         'neal'
 */
function MultiSelector(list_target, maxFields, currentFields, maxTitle, strInputName, objLanguage) {
	this.list_target = list_target;
	this.id = 0;
	this.count = (currentFields) ? currentFields : 0;
	this.max = (maxFields) ? maxFields : -1;
	this.maxtitle = (maxTitle) ? maxTitle : 5000;
	this.inputName = (strInputName) ? strInputName + "[]" : "files[]";
	this.objLanguage = (objLanguage) ? objLanguage : null;
	
	//*** Hide the empty file list.
	this.list_target.style.display = 'none';
	
	//*** Add a new file input element.
	this.addElement = function(element) {
		if (element.tagName.toUpperCase() == 'INPUT' && element.type == 'file') {
			this.id++;
			
			element.id = 'file_' + this.id;
			element.name = this.inputName;
			element.multi_selector = this;

			//*** What to do when a file is selected.
			element.onchange = function() {
				this.multi_selector.list_target.style.display = 'block';

				var new_element = document.createElement('input');
				new_element.type = 'file';
				new_element.className = 'input-textlarge';

				this.parentNode.insertBefore(new_element, this);

				this.multi_selector.addElement(new_element);
				this.multi_selector.addListRow(this);

				/*** 
				 * Appease Safari
				 * display:none doesn't seem to work correctly in Safari.
				 */
				this.style.position = 'absolute';
				this.style.left = '-1000px';
			}

			if (this.max != -1 && this.count >= this.max) {
				element.disabled = true;
			}

			this.count++;
			this.current_element = element;
		} else {
			//*** This can only be applied to file input elements!
			alert('Error: not a file input element');
		}
	}

	//*** Add a new row to the list of files.
	this.addListRow = function(element) {
		var new_row = document.createElement('div');
		new_row.className = 'multifile';
		new_row.element = element;

		var new_row_button = document.createElement('a');
		new_row_button.className = 'button';
		new_row_button.innerHTML = 'remove';
		new_row_button.href = '';

		//*** Delete function.
		new_row_button.onclick = function() {
			this.parentNode.element.parentNode.removeChild(this.parentNode.element);
			this.parentNode.parentNode.removeChild(this.parentNode);
			this.parentNode.element.multi_selector.count--;
			this.parentNode.element.multi_selector.current_element.disabled = false;
			
			if (this.parentNode.element.multi_selector.count == 1) {
				this.parentNode.element.multi_selector.list_target.style.display = 'none';
			}

			/*** 
			 * Appease Safari
			 * Without it Safari wants to reload the browser window
			 * which nixes your already queued uploads.
			 */
			return false;
		};

		new_row.appendChild(new_row_button);

		var new_row_value = document.createElement('p');
		var row_value = element.value;
		
		if (row_value.length > this.maxtitle) {
			//*** Get filename.
			var pathDelimiter = (row_value.search(/\\/gi) > -1) ? "\\" : "/";
			var arrPath = row_value.split(pathDelimiter);
			var strFile = arrPath.pop();
			
			//*** Calculate remaining length.
			var reminingLength = (this.maxtitle - strFile.length > 0) ? this.maxtitle - strFile.length : 3;
			
			var strPath = arrPath.join(pathDelimiter);
			row_value = strPath.substr(0, reminingLength) + "..." + pathDelimiter + strFile;
			new_row_value.title = element.value;
		}
		
		new_row_value.innerHTML = row_value;
		new_row.appendChild(new_row_value);

		this.list_target.appendChild(new_row);
	}
}