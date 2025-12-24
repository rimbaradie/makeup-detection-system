document.addEventListener('DOMContentLoaded', () => {
    // --- Global Elements & Utility Functions ---
    const navLinks = document.querySelectorAll('.main-nav .nav-link, .mobile-nav .nav-link');
const sections = document.querySelectorAll('main section');

navLinks.forEach(link => {
  link.addEventListener('click', e => {
    // لو الرابط فيه data-section نمنع إعادة تحميل الصفحة ونعرض القسم
    if (link.dataset.section) {
      e.preventDefault();

      const targetSection = link.dataset.section;

      // إظهار القسم المحدد وإخفاء الباقي
      sections.forEach(section => {
        if (section.id === targetSection) {
          section.classList.add('active-section');
        } else {
          section.classList.remove('active-section');
        }
      });

      // تعيين الرابط النشط
      navLinks.forEach(nav => nav.classList.remove('active'));
      link.classList.add('active');

      // لو عندك قائمة موبايل مفتوحة، تغلقها (اختياري)
      if (mobileMenuOverlay) {
        mobileMenuOverlay.style.display = 'none';
      }
    }
    // أما الروابط بدون data-section راح تفتح الصفحة عادي (بدون منع)
  });
});


    // Function to show a specific section
    function showSection(sectionId) {
        sections.forEach(section => {
            section.classList.remove('active-section');
            section.classList.add('hidden-section'); // Hide with display:none
        });
        const targetSection = document.getElementById(sectionId);
        if (targetSection) {
            targetSection.classList.remove('hidden-section'); // Remove display:none first
            setTimeout(() => { // Trigger animation after display change
                targetSection.classList.add('active-section');
            }, 50);
        }
    }

    // Function to set active navigation link
    function setActiveNavLink(linkToActivate) {
        navLinks.forEach(link => link.classList.remove('active'));
        if (linkToActivate) {
            linkToActivate.classList.add('active');
        }
    }

    // Function to open a specific modal
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex'; // Use flex to center
            document.body.style.overflow = 'hidden'; // Prevent scrolling
        }
    }

    // Function to close all modals
    function closeModals() {
        modals.forEach(modal => {
            modal.style.display = 'none';
        });
        document.body.style.overflow = ''; // Restore scrolling
    }

    // --- Event Listeners ---

    // Navigation clicks
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const sectionId = link.dataset.section;
            showSection(sectionId);
            setActiveNavLink(link);
            if (mobileMenuOverlay.classList.contains('open')) {
                mobileMenuOverlay.classList.remove('open');
            }
        });
    });

    // Initial section load (Home)
    showSection('home');
    setActiveNavLink(document.querySelector('.nav-link[data-section="home"]'));

    // Mobile menu toggle
    mobileMenuToggle.addEventListener('click', () => {
        mobileMenuOverlay.classList.add('open');
    });

    closeMobileMenu.addEventListener('click', () => {
        mobileMenuOverlay.classList.remove('open');
    });

    // Close modals using close button
    closeButtons.forEach(button => {
        button.addEventListener('click', closeModals);
    });

    // Close modals when clicking outside content
    window.addEventListener('click', (event) => {
        modals.forEach(modal => {
            if (event.target === modal) {
                closeModals();
            }
        });
    });

    // Scroll to section buttons on Hero
    document.querySelector('.scroll-to-analyzer').addEventListener('click', () => {
        showSection('analyzer');
        setActiveNavLink(document.querySelector('.nav-link[data-section="analyzer"]'));
    });

    document.querySelector('.scroll-to-products').addEventListener('click', () => {
        showSection('products');
        setActiveNavLink(document.querySelector('.nav-link[data-section="products"]'));
    });

    // --- Product Filtering ---
    const productGrid = document.getElementById('product-grid');
    const productCards = productGrid ? productGrid.querySelectorAll('.product-card') : [];
    const skinTypeFilter = document.getElementById('skin-type-filter');
    const skinToneFilter = document.getElementById('skin-tone-filter');
    const concernFilter = document.getElementById('concern-filter');
    const certificationFilter = document.getElementById('certification-filter');

    function applyFilters() {
        const selectedSkinType = skinTypeFilter.value;
        const selectedSkinTone = skinToneFilter.value;
        const selectedConcern = concernFilter.value;
        const selectedCertification = certificationFilter.value;

        productCards.forEach(card => {
            const cardSkinTypes = card.dataset.skinType.split(',');
            const cardSkinTones = card.dataset.skinTone.split(',');
            const cardConcerns = card.dataset.concerns.split(',');
            const cardCertifications = card.dataset.certifications.split(',');

            const matchesSkinType = selectedSkinType === 'all' || cardSkinTypes.includes(selectedSkinType);
            const matchesSkinTone = selectedSkinTone === 'all' || cardSkinTones.includes(selectedSkinTone);
            const matchesConcern = selectedConcern === 'all' || cardConcerns.includes(selectedConcern);
            const matchesCertification = selectedCertification === 'all' || cardCertifications.includes(selectedCertification);

            if (matchesSkinType && matchesSkinTone && matchesConcern && matchesCertification) {
                card.style.display = 'block'; // Show the card
            } else {
                card.style.display = 'none'; // Hide the card
            }
        });
    }

    if (skinTypeFilter) skinTypeFilter.addEventListener('change', applyFilters);
    if (skinToneFilter) skinToneFilter.addEventListener('change', applyFilters);
    if (concernFilter) concernFilter.addEventListener('change', applyFilters);
    if (certificationFilter) certificationFilter.addEventListener('change', applyFilters);

    // Initial filter application
    if (productCards.length > 0) {
        applyFilters();
    }


    // --- AI Skin Analyzer (Conceptual) ---
    const webcamFeed = document.getElementById('webcam-feed');
    const analyzerCanvas = document.getElementById('analyzer-canvas');
    const startScanBtn = document.getElementById('start-scan-btn');
    const captureBtn = document.getElementById('capture-btn');
    const retakeBtn = document.getElementById('retake-btn');
    const analyzerResults = document.querySelector('.analyzer-results');
    const resultSkinType = document.getElementById('result-skin-type');
    const resultConcerns = document.getElementById('result-concerns');
    const recommendedProductsList = document.getElementById('recommended-products-list');
    const saveAnalysisBtn = document.querySelector('.save-analysis-btn');

    let stream; // To hold the webcam stream

    // Dummy recommendations based on analysis (replace with real logic)
    const dummyRecommendations = {
        'oily': ['Oil-Free Matte Foundation', 'Balancing Primer', 'Blemish Control Concealer'],
        'dry': ['Hydrating Tinted Moisturizer SPF 30', 'Creamy Concealer', 'Dewy Finish Setting Spray'],
        'sensitive': ['Gentle Mineral Concealer', 'Hypoallergenic Mascara', 'Fragrance-Free Lip Balm'],
        'acne-prone': ['Oil-Free Matte Foundation', 'Blemish Control Concealer', 'Non-Comedogenic Powder'],
        'combination': ['T-Zone Mattifying Primer', 'Hydrating Tinted Moisturizer SPF 30', 'Oil-Free Matte Foundation'],
        'rosacea': ['Hydrating Tinted Moisturizer SPF 30', 'Green Color Corrector', 'Calming Face Powder'],
        'eczema': ['Hydrating Tinted Moisturizer SPF 30', 'Sensitive Skin Concealer']
    };

    startScanBtn.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            webcamFeed.srcObject = stream;
            webcamFeed.style.display = 'block';
            analyzerCanvas.style.display = 'none'; // Ensure canvas is hidden initially
            startScanBtn.style.display = 'none';
            captureBtn.style.display = 'inline-flex';
            retakeBtn.style.display = 'none';
            analyzerResults.style.display = 'none';
            alert('Camera access granted. Position your face in the center and click "Capture Photo".');
        } catch (err) {
            console.error("Error accessing webcam: ", err);
            alert("Could not access your webcam. Please ensure you have a camera and grant permission.");
        }
    });

    captureBtn.addEventListener('click', () => {
        if (!webcamFeed.srcObject) {
            alert('Please start the scan first.');
            return;
        }

        const context = analyzerCanvas.getContext('2d');
        analyzerCanvas.width = webcamFeed.videoWidth;
        analyzerCanvas.height = webcamFeed.videoHeight;
        context.clearRect(0, 0, analyzerCanvas.width, analyzerCanvas.height); // Clear previous content
        context.drawImage(webcamFeed, 0, 0, analyzerCanvas.width, analyzerCanvas.height);

        // Stop the webcam stream
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
        }
        webcamFeed.srcObject = null;
        webcamFeed.style.display = 'none';
        analyzerCanvas.style.display = 'block';

        // --- Mock AI Analysis ---
        const skinTypes = ['oily', 'dry', 'sensitive', 'acne-prone', 'combination'];
        const concerns = ['acne', 'rosacea', 'enlarged pores', 'redness'];
        const detectedSkinType = skinTypes[Math.floor(Math.random() * skinTypes.length)];
        const detectedConcerns = Array.from({ length: Math.floor(Math.random() * 3) + 1 }, () => concerns[Math.floor(Math.random() * concerns.length)]);

        resultSkinType.textContent = detectedSkinType.charAt(0).toUpperCase() + detectedSkinType.slice(1);
        resultConcerns.textContent = detectedConcerns.join(', ') || 'None identified';

        recommendedProductsList.innerHTML = '';
        const recs = dummyRecommendations[detectedSkinType] || dummyRecommendations['all'];
        recs.forEach(product => {
            const li = document.createElement('li');
            li.textContent = product;
            recommendedProductsList.appendChild(li);
        });
        // End Mock AI Analysis

        analyzerResults.style.display = 'block';
        captureBtn.style.display = 'none';
        retakeBtn.style.display = 'inline-flex';
        startScanBtn.style.display = 'none'; // Keep Start Scan hidden
        alert('Analysis complete! Check your results below.');
    });

    retakeBtn.addEventListener('click', () => {
        webcamFeed.style.display = 'block';
        analyzerCanvas.style.display = 'none';
        analyzerResults.style.display = 'none';
        startScanBtn.style.display = 'inline-flex'; // Show Start Scan again
        captureBtn.style.display = 'none';
        retakeBtn.style.display = 'none';
    });

    saveAnalysisBtn.addEventListener('click', () => {
        alert('Analysis saved to your profile! (Conceptual)');
        // In a real application: Send the analysis results (image, skin type, concerns, recommendations) to a backend for user profile storage.
        // Update the 'My Profile' > 'Saved Analyses' tab dynamically.
    });


    // --- Skin Type Quiz ---
    const quizStartBtn = document.querySelector('.quiz-start-btn');
    const skinQuizModal = document.getElementById('skin-quiz-modal');
    const skinQuizForm = document.getElementById('skin-quiz-form');
    const quizQuestions = skinQuizForm.querySelectorAll('.quiz-question');
    const prevQuestionBtn = skinQuizForm.querySelector('.prev-question');
    const nextQuestionBtn = skinQuizForm.querySelector('.next-question');
    const submitQuizBtn = skinQuizForm.querySelector('.submit-quiz');
    const quizResultsDiv = document.getElementById('quiz-results');
    const quizResultType = document.getElementById('quiz-result-type');
    const quizRecommendedProductsList = document.getElementById('quiz-recommended-products-list');
    const viewRecommendedProductsBtn = document.querySelector('.view-products-btn');


    let currentQuestionIndex = 0;

    function showQuizQuestion(index) {
        quizQuestions.forEach((q, i) => {
            q.classList.remove('active-question');
            if (i === index) {
                q.classList.add('active-question');
            }
        });

        prevQuestionBtn.style.display = (index === 0) ? 'none' : 'inline-flex';
        nextQuestionBtn.style.display = (index === quizQuestions.length - 1) ? 'none' : 'inline-flex';
        submitQuizBtn.style.display = (index === quizQuestions.length - 1) ? 'inline-flex' : 'none';
    }

    quizStartBtn.addEventListener('click', () => {
        skinQuizForm.reset();
        quizResultsDiv.style.display = 'none';
        currentQuestionIndex = 0;
        showQuizQuestion(currentQuestionIndex);
        openModal('skin-quiz-modal');
    });

    nextQuestionBtn.addEventListener('click', () => {
        const currentQuestion = quizQuestions[currentQuestionIndex];
        const selectedOption = currentQuestion.querySelector('input[type="radio"]:checked');
        if (!selectedOption) {
            alert('Please select an option before proceeding.');
            return;
        }

        if (currentQuestionIndex < quizQuestions.length - 1) {
            currentQuestionIndex++;
            showQuizQuestion(currentQuestionIndex);
        }
    });

    prevQuestionBtn.addEventListener('click', () => {
        if (currentQuestionIndex > 0) {
            currentQuestionIndex--;
            showQuizQuestion(currentQuestionIndex);
        }
    });

    skinQuizForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const currentQuestion = quizQuestions[currentQuestionIndex];
        const selectedOption = currentQuestion.querySelector('input[type="radio"]:checked');
        if (!selectedOption) {
            alert('Please select an option for the last question.');
            return;
        }

        // --- Simple Quiz Logic (Conceptual) ---
        const answers = {};
        quizQuestions.forEach((q, i) => {
            const name = q.querySelector('input[type="radio"]').name;
            const value = q.querySelector('input[name="' + name + '"]:checked')?.value;
            answers[name] = value;
        });

        let deducedSkinType = 'Normal'; // Default
        let recommendations = dummyRecommendations['all'];

        if (answers.q1 === 'tight-dry') {
            deducedSkinType = 'Dry';
            recommendations = dummyRecommendations['dry'];
        } else if (answers.q1 === 'shiny-all-over') {
            deducedSkinType = 'Oily';
            recommendations = dummyRecommendations['oily'];
        } else if (answers.q1 === 'oily-tzone') {
            deducedSkinType = 'Combination';
            recommendations = dummyRecommendations['combination'];
        }

        if (answers.q2 === 'frequently' || answers.q2 === 'tzone-only') {
            if (deducedSkinType !== 'Oily' && deducedSkinType !== 'Combination') {
                 deducedSkinType = 'Acne-Prone'; // Overwrite if breakouts are primary
            }
            if (!recommendations.includes('Blemish Control Concealer')) { // Add if not already there
                recommendations = [...recommendations, 'Blemish Control Concealer'];
            }
        }

        if (answers.q3 === 'redness-itching') {
            deducedSkinType = 'Sensitive'; // Prioritize sensitive if reaction
            recommendations = dummyRecommendations['sensitive'];
        }

        quizResultType.textContent = deducedSkinType;
        quizRecommendedProductsList.innerHTML = '';
        recommendations.forEach(product => {
            const li = document.createElement('li');
            li.textContent = product;
            quizRecommendedProductsList.appendChild(li);
        });
        // End Simple Quiz Logic

        skinQuizForm.style.display = 'none';
        quizResultsDiv.style.display = 'block';
    });

    viewRecommendedProductsBtn.addEventListener('click', () => {
        closeModals();
        showSection('products');
        setActiveNavLink(document.querySelector('.nav-link[data-section="products"]'));
        // Optionally, apply quiz-derived filters automatically
        skinTypeFilter.value = quizResultType.textContent.toLowerCase().replace(' ', '-');
        applyFilters();
    });

    


    // --- User Reviews & Ratings ---
    const addReviewBtns = document.querySelectorAll('.btn-add-to-cart'); // For demo, let's use cart button for review trigger
    const reviewModal = document.getElementById('review-modal');
    const reviewForm = document.getElementById('review-form');
    const reviewProductId = document.getElementById('review-product-id');
    const reviewProductName = document.getElementById('review-product-name');
    const reviewStars = document.getElementById('review-stars');
    const reviewRatingInput = document.getElementById('review-rating');

    // Dynamically open review modal when "Add to Cart" is clicked (for demonstration purposes)
    addReviewBtns.forEach(button => {
        button.addEventListener('click', (e) => {
            // In a real scenario, this would be a "Leave a Review" button or a prompt after purchase.
            // For now, we'll use this to trigger the modal.
            const productCard = e.target.closest('.product-card');
            const productId = e.target.dataset.productId;
            const productName = productCard.querySelector('h3').textContent;

            reviewProductId.value = productId;
            reviewProductName.value = productName;
            reviewForm.reset();
            reviewRatingInput.value = 0;
            reviewStars.querySelectorAll('i').forEach(star => star.classList.replace('fas', 'far')); // Reset stars

            openModal('review-modal');
            alert(`Added "${productName}" to cart! Now, please leave a review! (Demo)`);
        });
    });

    // Star rating functionality
    if (reviewStars) {
        reviewStars.addEventListener('click', (e) => {
            if (e.target.tagName === 'I') {
                const value = parseInt(e.target.dataset.value);
                reviewRatingInput.value = value;
                reviewStars.querySelectorAll('i').forEach((star, index) => {
                    if (index < value) {
                        star.classList.replace('far', 'fas');
                    } else {
                        star.classList.replace('fas', 'far');
                    }
                });
            }
        });
    }

    reviewForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const productId = reviewProductId.value;
        const productName = reviewProductName.value;
        const skinType = document.getElementById('review-skin-type').value;
        const skinTone = document.getElementById('review-skin-tone').value;
        const rating = reviewRatingInput.value;
        const reviewText = document.getElementById('review-text').value.trim();

        if (rating === '0' || reviewText === '') {
            alert('Please provide a rating and a review message.');
            return;
        }

        alert(`Review submitted for "${productName}" (ID: ${productId}) with rating ${rating} and text: "${reviewText}". (Conceptual)`);
        console.log({ productId, productName, skinType, skinTone, rating, reviewText });
        closeModals();
        // In a real application, send this data to your backend.
        // Update the 'My Reviews' tab dynamically.
    });


    // --- User Profile Tabs ---
    const profileTabs = document.querySelectorAll('.profile-tabs .tab-button');
    const profileTabContents = document.querySelectorAll('.profile-section .tab-content');

    profileTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            profileTabs.forEach(t => t.classList.remove('active'));
            profileTabContents.forEach(content => content.classList.remove('active'));

            tab.classList.add('active');
            const targetId = tab.dataset.tab;
            document.getElementById(targetId).classList.add('active');
        });
    });

    // Profile Settings Form
    const accountSettingsForm = document.querySelector('.account-settings-form');
    if (accountSettingsForm) {
        accountSettingsForm.addEventListener('submit', (e) => {
            e.preventDefault();
            alert('Account settings saved! (Conceptual)');
            // In a real app, send data to backend
        });
    }

    // Example delete functionality for saved analyses / reviews
    document.querySelectorAll('.delete-analysis').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (confirm('Are you sure you want to delete this analysis?')) {
                e.target.closest('.analysis-card').remove();
                alert('Analysis deleted. (Conceptual)');
            }
        });
    });

    document.querySelectorAll('.delete-review').forEach(btn => {
        btn.addEventListener('click', (e) => {
            if (confirm('Are you sure you want to delete this review?')) {
                e.target.closest('.user-review-card').remove();
                alert('Review deleted. (Conceptual)');
            }
        });
    });

    document.querySelectorAll('.edit-review').forEach(btn => {
        btn.addEventListener('click', () => {
            alert('Edit review functionality would open the review modal pre-filled. (Conceptual)');
            // Populate review modal with existing review data and open it
        });
    });

    // --- Ingredient Checker Tool (Conceptual) ---
    const analyzeIngredientsBtn = document.getElementById('analyze-ingredients-btn');
    const ingredientListInput = document.getElementById('ingredient-list-input');
    const ingredientResultsDiv = document.querySelector('.ingredient-results');
    const ingredientBreakdownDiv = document.getElementById('ingredient-breakdown');
    const concernSuitabilityDiv = document.querySelector('.concern-suitability');

    analyzeIngredientsBtn.addEventListener('click', () => {
        const inputText = ingredientListInput.value.trim();
        if (!inputText) {
            alert('Please paste an ingredient list to analyze.');
            return;
        }

        // Simulating ingredient parsing and analysis
        const ingredients = inputText.split(',').map(s => s.trim()).filter(s => s.length > 0);
        let breakdownHTML = '<h4>Ingredients Breakdown:</h4>';
        let hasComedogenic = false;
        let hasIrritants = false;

        ingredients.forEach(ing => {
            let note = '';
            // Very simplified mock logic
            if (ing.toLowerCase().includes('parfum') || ing.toLowerCase().includes('fragrance')) {
                note = '<span class="note">(Common Irritant for Sensitive Skin)</span>';
                hasIrritants = true;
            } else if (ing.toLowerCase().includes('alcohol denat')) {
                note = '<span class="note">(Drying, Potentially Irritating)</span>';
                hasIrritants = true;
            } else if (ing.toLowerCase().includes('coconut oil') || ing.toLowerCase().includes('isopropyl myristate')) {
                note = '<span class="note">(Highly Comedogenic - can clog pores)</span>';
                hasComedogenic = true;
            } else if (ing.toLowerCase().includes('salicylic acid') || ing.toLowerCase().includes('niacinamide')) {
                 note = '<span class="note">(Beneficial for Acne/Oil Control)</span>';
            } else if (ing.toLowerCase().includes('hyaluronic acid') || ing.toLowerCase().includes('glycerin')) {
                note = '<span class="note">(Hydrating)</span>';
            }
            breakdownHTML += `<p><strong>${ing}</strong> ${note}</p>`;
        });

        ingredientBreakdownDiv.innerHTML = breakdownHTML;

        // Update suitability messages
        let suitabilityHTML = '<h4>Suitability for Specific Concerns:</h4>';
        suitabilityHTML += `<p><i class="fas ${hasComedogenic ? 'fa-times-circle danger-icon' : 'fa-check-circle success-icon'}"></i> <span class="concern-text">${hasComedogenic ? 'Not Recommended' : 'Suitable'} for Acne-Prone Skin</span> (${hasComedogenic ? 'Contains known pore-clogging ingredients.' : 'No known comedogenic ingredients found.'})</p>`;
        suitabilityHTML += `<p><i class="fas ${hasIrritants ? 'fa-exclamation-triangle warning-icon' : 'fa-check-circle success-icon'}"></i> <span class="concern-text">${hasIrritants ? 'Use with Caution' : 'Generally Suitable'} for Sensitive Skin</span> (${hasIrritants ? 'Contains potential irritants.' : 'No common irritants found.'})</p>`;

        // Add more suitability checks as needed (e.g., for rosacea, dry skin, etc.)
        // This is where integration with a larger ingredient database would be powerful.

        concernSuitabilityDiv.innerHTML = suitabilityHTML;

        ingredientResultsDiv.style.display = 'block';
        alert('Ingredients analyzed! See the breakdown below.');
    });

    // --- Newsletter Subscription (Conceptual) ---
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const emailInput = newsletterForm.querySelector('input[type="email"]');
            if (emailInput.value.trim() !== '') {
                alert(`Thank you for subscribing, ${emailInput.value}!`);
                emailInput.value = '';
                // In a real app, send email to your mailing list service
            } else {
                alert('Please enter a valid email address.');
            }
        });
    }

    // --- Before & After Gallery Filtering (Conceptual) ---
    const gallerySkinConditionFilter = document.getElementById('gallery-skin-condition');
    const galleryItems = document.querySelectorAll('.gallery-item');

    if (gallerySkinConditionFilter) {
        gallerySkinConditionFilter.addEventListener('change', () => {
            const selectedCondition = gallerySkinConditionFilter.value;
            galleryItems.forEach(item => {
                const itemCondition = item.dataset.condition;
                if (selectedCondition === 'all' || itemCondition === selectedCondition) {
                    item.style.display = 'flex'; // Show
                } else {
                    item.style.display = 'none'; // Hide
                }
            });
        });
    }

    // Initial gallery filter apply
    if (galleryItems.length > 0) {
        gallerySkinConditionFilter.dispatchEvent(new Event('change'));
    }
});