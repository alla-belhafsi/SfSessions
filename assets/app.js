import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css'

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉')

{/* <script>
            function addProgrammeForm(button) {
                var container = document.getElementById('programmes-container');
                var prototype = container.getAttribute('data-prototype');
                var index = container.children.length; // Nombre actuel de formulaires
                var newForm = prototype.replace(/__name__/g, index);
                
                // Créer un élément div pour le nouveau formulaire
                var newFormDiv = document.createElement('div');
                newFormDiv.innerHTML = newForm;
        
                // Ajouter des classes Bootstrap pour le style aux champs individuels
                newFormDiv.querySelectorAll('.form-group').forEach(function(formGroup) {
                    formGroup.classList.add('col-sm', 'mt-3');
                });
        
                // Ajouter le nouveau formulaire avant le bouton "Ajouter un programme"
                container.insertBefore(newFormDiv, button.parentElement.parentElement);
            }
        </script> */}