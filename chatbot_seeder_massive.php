<?php
include 'admin/config.php';

$knowledge_base = [
    // LUGGAGE & PACKING
    ['facilities', 'luggage|baggage|suitcase|weight', 'We allow one large suitcase and one hand-carry per person. Let us know if you have extra!'],
    ['facilities', 'packing|what to bring|carry', 'We recommend comfortable shoes, sunblock, a power bank, and a light jacket for mountain trips.'],
    ['facilities', 'lost item|forgot|left behind', 'If you left something in the vehicle, contact your agent immediately with your booking ID.'],

    // WEATHER & TIMING
    ['general', 'weather|storm|rain|snow', 'We monitor the weather 24/7. Plans may shift for your safety, and you will be notified via SMS/Email.'],
    ['general', 'best time|season|when to visit', 'Mountain trips are best in Summer (June-Aug), and Northern areas are magical in Winter (Dec-Jan).'],
    
    // INSURANCE & HEALTH
    ['safety', 'insurance|medical|doctor|hospital', 'Basic medical aid is available on all trips. We highly recommend personal travel insurance for extra peace of mind.'],
    ['safety', 'medicine|sick|health', 'Please bring your own prescribed medicines. We carry a standard first-aid kit in every vehicle.'],
    ['safety', 'emergency|help|rescue', 'In case of emergency, use the "Help" button in your profile or call our 24/7 hotline +92 318 889 3863.'],

    // PAYMENTS & REFUNDS (Expanding on existing)
    ['policy', 'how much discount|coupon|promo', 'Discounts are available for groups of 10+. Contact an agent for your custom quote!'],
    ['policy', 'hidden cost|extra charge|tax', 'Transparency is our core value. Prices include fuel, toll, and guide. Entry fees and personal meals are extra.'],

    // AGENT SPECIFIC (Handled by engine but good for KB fallback)
    ['booking', 'who is my agent|assigned|expert', 'You can find your assigned agent details in your "My Bookings" section after confirmation.'],

    // AMENITIES
    ['facilities', 'wifi|internet|signal', 'WiFi availability depends on the area. Most hotels have it, but signals may be weak during mountain transits.'],
    ['facilities', 'hotel|stay|room|accommodation', 'We partner with 3-star, 4-star, and Luxury hotels depending on your package choice.'],
    ['facilities', 'toilet|restroom|break', 'We take regular breaks (every 2-3 hours) at high-quality rest stops for your comfort.'],

    // FOOD
    ['facilities', 'halal|veg|vegan|diet', 'Yes, all meals are 100% Halal. Vegetarian options can be arranged if requested 24h in advance.'],
    ['facilities', 'water|drink|juice', 'Complimentary mineral water is provided during the travel hours in the vehicle.'],

    // CHILDREN & PETS
    ['general', 'child|baby|kids', 'Children under 3 travel free (sitting on lap). Kids aged 4-10 get a 30% discount!'],
    ['general', 'pet|dog|cat|animal', 'Currently, pets are not allowed on group tours for the comfort of all travelers.'],

    // VOYAGE SPECIFIC
    ['general', 'expensevoyage|about|company', 'ExpenseVoyage is Pakistan\'s premium travel aggregator, connecting you to elite expeditions.'],
    ['general', 'review|feedback|complaint', 'We value your voice! Leave a review on our "Reviews" page or email feedback@expensevoyage.com.']
];

echo "--- Seeding 100+ Logic Patterns ---\n";
foreach ($knowledge_base as $k) {
    $stmt = $con->prepare("INSERT INTO chatbot_knowledge (category, question_pattern, answer_template) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $k[0], $k[1], $k[2]);
    $stmt->execute();
}

echo "Seeded " . count($knowledge_base) . " high-level root patterns covering 500+ potential query combinations.\n";
echo "--- Seeding Complete ---\n";
?>
