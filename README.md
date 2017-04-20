TDD Session
==============

An example PHP project (powered by Symfony) in order to learn and practice TDD.

The goal here is to calculate sub matrix with different params such as:
- Horizontal position of the center square
- Vertical position of the center square
- Range / Wide of the sub matrix
 
I tried to following certain patterns as Clean Code Book [1] suggests and TDD.
 
# Usage

Just run 
<pre>
    cd pathToProject
    
    composer install
    
    ./bin/console server:run
</pre>

and go to <a href="http://127.0.0.1:8000">http://127.0.0.1:8000</a>.

You can control the main variables (Middle square, range and side of the matrix) inside the
[Default Controller](src/AppBundle/Controller/DefaultController.php).

# System Requirements
 
Dependencies used in this project are:

- PHP 7.1.4
- Symfony 3

Check _composer.json_ to get more info

Enjoy

[1] <a href="https://www.amazon.com/Clean-Code-Handbook-Software-Craftsmanship/dp/0132350882"> Clean Code at Amazon </a>
    <a href="https://en.wikipedia.org/wiki/Robert_Cecil_Martin">Author: Robert Cecil Martin</a>