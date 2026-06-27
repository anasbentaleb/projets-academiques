// Function to open the modal and populate data
function openModal(name, type, age, desc, img, id, status, hasRequested) {
    // 1. Get Elements
    const modal = document.getElementById('petModal');
    const modalImg = document.getElementById('modalImg');
    const modalName = document.getElementById('modalName');
    const modalType = document.getElementById('modalType');
    const modalAge = document.getElementById('modalAge');
    const modalDesc = document.getElementById('modalDesc');
    const modalPetId = document.getElementById('modalPetId');
    const modalBtn = document.getElementById('modalBtn');
    const modalMsg = document.getElementById('modalMsg');

    // 2. Set Content
    modal.style.display = "block";
    modalImg.src = img;
    modalName.innerText = name;
    modalType.innerText = type;
    modalAge.innerText = age;
    modalDesc.innerText = desc;
    modalPetId.value = id; // Set hidden input for the form

    // 3. Handle Button State (Logic)
    // Reset previous states
    modalBtn.style.display = 'block';
    modalBtn.disabled = false;
    modalBtn.innerText = "Request Adoption";
    modalBtn.style.background = "#0284c7"; // Default Blue
    modalMsg.style.display = 'none';

    if (status === 'adopted') {
        modalBtn.disabled = true;
        modalBtn.innerText = "Already Adopted";
        modalBtn.style.background = "#ccc";
    } 
    else if (hasRequested > 0) {
        // If user already requested this pet
        modalBtn.disabled = true;
        modalBtn.innerText = "Request Pending...";
        modalBtn.style.background = "#f59e0b"; // Orange/Yellow
        modalMsg.innerText = "You have already sent a request for this pet.";
        modalMsg.style.display = 'block';
    }
}

// Function to close the modal
function closeModal() {
    const modal = document.getElementById('petModal');
    modal.style.display = "none";
}

// Close modal if user clicks outside the white box
window.onclick = function(event) {
    const modal = document.getElementById('petModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}