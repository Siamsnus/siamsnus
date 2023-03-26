<pre>
<code>
&lt;h1&gt;Siamsnus Gästkonvertering&lt;/h1&gt;
&lt;h2&gt;Beskrivning&lt;/h2&gt;
Siamsnus Gästkonvertering är ett WordPress-plugin för WooCommerce-webbplatser som konverterar gästkonton till riktiga kundkonton vid inloggning. Pluginet hjälper till att koppla ihop tidigare ordrar från gästkunder med deras nyskapade användarkonton och förenklar hanteringen av kunddata.

&lt;h2&gt;Hur det fungerar&lt;/h2&gt;
När en användare försöker logga in kontrollerar pluginet först om det är en administratör som loggar in, och om så är fallet avbryts processen. Annars fortsätter den med att kontrollera om användaren redan existerar eller om användarnamn och lösenord är tomma. I sådana fall visas lämpliga felmeddelanden.

Pluginet kontrollerar om användarnamnet är en e-postadress eller ett användarnamn. Om det är ett användarnamn valideras lösenordet och en giltig inloggning returneras om lösenordet stämmer överens. Annars returneras ett felmeddelande.

Om det är en e-postadress hämtar pluginet alla ordrar för den angivna e-postadressen och räknar antalet ordrar.

Om det finns ordrar skapas ett nytt användarkonto för användaren med ett slumpmässigt genererat lösenord. Användarens detaljer, roll, förnamn och efternamn uppdateras från den första ordern i listan över tidigare ordrar.

Användarens WooCommerce-fakturerings- och leveransdata kopplas till användaren genom att uppdatera användarmeta-data.

Alla tidigare ordrar kopplas till den nyligen genererade användaren och användaren får ett felmeddelande om att de behöver återställa sitt lösenord av säkerhetsskäl.

När en ny användare registrerar sig kopplar pluginet tidigare ordrar från deras e-postadress till det nya kundkontot.

&lt;h2&gt;Installation&lt;/h2&gt;
Ladda ner pluginet och lägg till det i din WordPress wp-content/plugins-mapp. Aktivera sedan pluginet via WordPress-administratörspanelen.
</code>
</pre>


