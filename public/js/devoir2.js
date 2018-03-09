'use strict';

/*
 * This class creates a form by manipulating the DOM.
 *
 * It works by adding fields and relations to other resultsets
 * and by adding the form structure to the DOM.
 *
 */
class Form {

   constructor(formname, method, action) {
      this.name   = formname;
      this.method = method;
      this.action = action;
      this.fields = new Array();
   }

   /*
    * Adds a field in the form to be build.
    */
   addField(name, type, value) {

      if (name == 'pays') {
         var select_pays = new SelectField(name, value);
      
         this.fields.push(
            select_pays
         );

         select_pays.addElement('Belgique', 'Belgique');
         select_pays.addElement('France', 'France', 1);
         select_pays.addElement('Suisse', 'Suisse');

         return;
      }

      if (name == 'codepostal') {
         
         this.fields.push(
            new AreaCodeField(name, value)
         );

         return;
      }

      if (name == 'telephone') {
         
         this.fields.push(
            new TelField(name, value)
         );

         return;
      }

      if (name == 'quantite') {
         
         this.fields.push(
            new IntField(name, value, 'number', 1, 999999)
         );

         return;
      }



      if (type == 'DATE') {
         this.fields.push(
            new DateField(name, value, 'date')
         );
      }
      else if (type == 'INT') {
         this.fields.push(
            new IntField(name, value, 'number')
         );
      }
      else if (type == 'FLOAT') {
         this.fields.push(
            new FloatField(name, value, 'number')
         );
      }
      else if (type.match(/VARCHAR\((\d+)\)/)) {
         var match = type.match(/VARCHAR\((\d+)\)/);

         console.log(match[1]);

         if (match[1] > 100) { 
            this.fields.push(
               new TextAreaField(name, value, match[1])
            ); 
         }
         else {
            this.fields.push(
               new TextField(name, value, match[1])
            );
         }
      }


   }

   /*
    * Adds a relation as a select field
    */
   addRelation(name, type, required, value) {

      var select = new SelectField(name, value);

      this.fields.push(
         select
      );

      return select;

   }

   /*
    * Appends the form to the DOM
    */
   appendToDOM(submitname, resetname) {

      var form = document.createElement('form');
      form.setAttribute('id',       this.name);
      form.setAttribute('method',   this.method);
      form.setAttribute('action',   this.action);
      form.setAttribute('class',    'form');

      var formname = this.name;

      this.fields.forEach(function(element) {
         
         form.appendChild(
            element.createDOMElement(formname)
         );
      
      });
     
      var reset = document.createElement('input');
      reset.setAttribute('type', 'reset');
      reset.setAttribute('value', resetname);

      var submit = document.createElement('input');
      submit.setAttribute('type', 'button');
      submit.setAttribute('value', submitname);
      
      var formObject = this;

      submit.onclick = function(){ formObject.submitForm(); };

      form.appendChild(reset);
      form.appendChild(submit);
      
      document.getElementById('container').appendChild(form);

   }

   /*
    * This method is triggered on the submission of the form, it can validate 
    * further the data by implementing form fields types and calling the 
    * validateInput method on the specific field type.
    */
   submitForm() {
      
      var canSubmit = true;

      this.fields.forEach(function(element) {

         element.validateInput(document.getElementById(element.dom_id));

         if (element.valid == false) {
            element.addHelp();
            canSubmit = false;
         }

      });
      
      if (canSubmit) {
         document.getElementById(this.name).submit();
      }

   }

}





/*
 * This class represent a generic field in a form.
 *
 */
class Field {

   constructor(name, value, type = 'text', required) {
      this.name      = name;
      this.value     = value;
      this.required  = required;
      this.valid     = false;
      this.type      = type;
      this.dom_id    = '';

   }


  createDOMElement(formname) {
     
      this.dom_id = formname + '_' + this.name;

      var div = document.createElement('div');
      div.setAttribute('class',   'form-group');

      var label = document.createElement('label');
      label.setAttribute('for', this.dom_id);
      label.innerHTML = this.name;

      var input = this.createInputElement(); 
      
      input.setAttribute('id',            this.dom_id);
      input.setAttribute('name',          this.name);
      input.setAttribute('placeholder',   this.name);
      input.setAttribute('value',         this.value);
      input.setAttribute('class',         'form-control');
      
      var inputObject = this;

      input.onkeydown = function(){ 
         inputObject.validateInput(this); 
      };

      div.appendChild(label);
      div.appendChild(input);

      return div;

   }

  
   createInputElement()   {
   
      var input = document.createElement('input');
      input.setAttribute('type',          this.type);
      return input;
   }

   /*
    * Template method
    */
   validateInput()   { }
   addHelp()         { }

}



/*
 * Generic field to input text data
 */
class TextField extends Field {

   constructor(name, value, maxsize) {
      super(name, value);
      this.maxsize      = maxsize;
      this.minsize      = 2;

   }

   createInputElement()   {
      var input = document.createElement('input');
      input.setAttribute('type',    this.type);
      input.setAttribute('pattern', '.{' + this.minsize + ',' + this.maxsize + '}');
      return input;
   }

   validateInput() {
      this.valid = true;
   }

   addHelp() {
      console.log("Help " + this.name);
   }
}


/*
 * A tel field to input a phone number
 */
class TelField extends Field {

   constructor(name, value, maxsize) {
      super(name, value);
   }

   createInputElement()   {   
      var input = document.createElement('input');
      input.setAttribute('type',    'tel');
      input.setAttribute('pattern', '^[0-9\-\+\s\(\)]*$');
      return input;
   }

   validateInput() {
      this.valid = true;
   }

   addHelp() {
      console.log("Help " + this.name);
   }
}

/*
 * A textarea to input long texts
 */
class TextAreaField extends Field {

   constructor(name, value, maxsize) {
      super(name, value);
      this.maxsize      = maxsize;
      this.minsize      = 2;

   }

   createInputElement()   {
      var input = document.createElement('textarea');   
      input.innerHTML = this.value;
      return input;
   }

   validateInput() {
      this.valid = true;
   }

   addHelp() {
      console.log("Help " + this.name);
   }
}


/*
 * A field to input integers
 */
class IntField extends Field {

   constructor(name, value, type, min, max) {
      super(name, value, type);
      this.min      = min;
      this.max      = max;
      this.type     = type;
   }

   createInputElement()   {
   
      var input = document.createElement('input');
      input.setAttribute('type',          'number');
      input.setAttribute('min',           this.min);
      input.setAttribute('max',           this.max);   
      return input;
   }

   validateInput(e) {
      if (e.value >= this.min && e.value <= this.max) {
         this.valid = true;
         document.getElementById(this.dom_id).classList.add("input_valid");
         document.getElementById(this.dom_id).classList.remove("input_invalid");
      }
      else {
         this.valid = false;
         document.getElementById(this.dom_id).classList.remove("input_valid");
         document.getElementById(this.dom_id).classList.add("input_invalid");
      }
   }

}



/*
 * A specific int field to input areacode
 */
class AreaCodeField extends IntField {

   constructor(name, value, type) {
      super(name, value, type);
      
      // http://www.fedex.com/ch_francais/services/pstlcds.html
      this.min      = 1000;
      this.max      = 99999;
   }

   // 1 peu double usage .. mais ca peux servir ..
   validateInput(e) {
      if (e.value >= this.min && e.value <= this.max) {
         this.valid = true;
         document.getElementById(this.dom_id).classList.add("input_valid");
         document.getElementById(this.dom_id).classList.remove("input_invalid");
      }
      else {
         this.valid = false;
         document.getElementById(this.dom_id).classList.remove("input_valid");
         document.getElementById(this.dom_id).classList.add("input_invalid");
      }
   }

   addHelp() {
      console.log("Help " + this.name);
   }

}



/*
 * Unused ..
 */
class FloatField extends TextField {

   constructor(name, value) {
      super(name, value);
   }

   validateInput() {
      this.valid = true;
   }

}


/*
 * A date field with an html5 validator
 */
class DateField extends Field {

   constructor(name, value) {
      super(name, value);
   }

   createInputElement()   {   
      var input = document.createElement('input');
      input.setAttribute('type',    'date');
      input.setAttribute('pattern', '[0-9]{4}.(0[1-9]|1[012]).(0[1-9]|1[0-9]|2[0-9]|3[01])');
      return input;
   }

   validateInput() {
      this.valid = true;
   }

   addHelp() {
      console.log("Help " + this.name);
   }

}


/*
 * A select in which elements can be added with the addElement method
 */
class SelectField extends Field {

   constructor(name, value) {
      super(name, value);
      this.options = new Array();
   }

  createDOMElement(formname) {

      this.dom_id = formname + '_' + this.name;

      var div = document.createElement('div');
      div.setAttribute('class',   'form-group');

      var label = document.createElement('label');
      label.setAttribute('for', this.dom_id);
      label.innerHTML = this.name;

      var select = document.createElement('select');
      select.setAttribute('type',   'text');
      select.setAttribute('name',   this.name);
      select.setAttribute('id',     this.dom_id);
      select.setAttribute('class',  'form-control');

      this.options.forEach(function(element) {

         var option = document.createElement("option");
         option.text    = element[0];
         option.value   = element[1];
         if (element[2]) {
            option.setAttribute('selected',   'true');
         }
         select.add(option); 

      });
      
      div.appendChild(label);
      div.appendChild(select);

      return div;

  }


   addElement(name, value, selected) {
      this.options.push(
         new Array(name, value, selected)
      );
   }

   validateInput() {
      this.valid = true;
   }

   addHelp() {
      console.log("Help " + this.name);
   }


}



