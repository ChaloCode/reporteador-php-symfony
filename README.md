Colibrí Report

Es un proyecto realizado en Symfony 3.0.4 pensado para la toma decisión, en el cual se podrá generar: 
Reportes dinámicos: Tabla de información, con filtros por campo.
Gráficas: Gráficas de barras, líneas, área.
Predicciones de mercado: Apoyado modelos matemáticos; regresión lineal, exponencial y cuadrática.

Requisitos 
Tener configurado el ambiente de Symfony
https://symfony.com/doc/current/setup.html 

Instalación 
Una vez tenga la clonación del repositorio:
Ejecute: composer install
Copie: reporteador-php-symfony/vendor modificado_ok/friendsofsymfony/user-bundle/ a la carpeta de vendor local
Ejecute: php bin/console doctrine:schema:update --force
Restaure la base de datos reporteador-php-symfony/vendor modificado_ok/tab from db system/

Ejecutar o correr el servidor
php bin/console server:start



