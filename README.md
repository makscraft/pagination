# pagination
Pagination manager for Symfony application.

Works with urls by adding the numbers of pages to them.

Example of use in controller of repository:
$pagination = new Pagination(100, 20);
$repository -> findBy([...], [...], $pagination -> getLimit(), $pagination -> getOffset());
  
Returns array of links for html template.
$pagination -> setPath('/catalog') -> getDisplayData();

Result links will look like: /catalog?page=3

For twig template engine.

Use package twig template or copy it into templates directory of your project.
Inside the controller pass the pagination object into template.

return $this -> render('mypage.html.twig', [
    
    ...
    
    'pagination' => $pagination
]);

To use template from vendor directory add the path into twig settings file.
//config/packages/twig.yaml    
paths:
    'vendor/maxizdev/pagination/templates': 'pagination'


Next include the pagination template in your twig files.
{% include '@pagination/pagination.html.twig' %}

Also you can copy the template file into templates directory and use it as you like.
File location: vendor/maxizdev/pagination/templates/pagination.html.twig