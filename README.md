


***WEB API DOCUMENTATION***
----
* **URL**
   
   /api/items
   
   Returns all visible cars item. By default ordered by price

* **Method:**
  
  `GET`

*  **URL Params**
 
   `minPrice=[integer]`&`maxPrice=[integer]`

* **Success Response:**
  
  * **Code:** 200 <br />
  
 * **URL**
     
     /api/items/{id}
     
      Returns a specified car by Id  
  
  * **Method:**
    
    `GET`
  
  *  **URL Params**
   
     **Required:**
      
     `id=[integer]`
  
  * **Success Response:**
    
    * **Code:** 200 <br />
  
  * **Not Found Response:**
    * **Code:** 404 <br />
 
