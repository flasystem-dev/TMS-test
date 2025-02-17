
<!-- Add a text input and a button to trigger the NLP processing -->
<input type="text" id="inputText" placeholder="Enter your text">11
<button onclick="processNLP()">Process</button>
<!-- Display the result -->
<div id="result"></div>
<script>
    function processNLP() {
        const text = document.getElementById('inputText').value;

        // Send a POST request to the Laravel backend
        fetch('/process-nlp', {
            method: 'POST',
            headers: {
                'Content-Type’: 'application/json’,
        'X-CSRF-TOKEN’: '{{ csrf_token() }}'
    },
        body: JSON.stringify({ text: text })
    })
    .then(response => response.json())
            .then(data => {
                // Display the result in the 'result' div
                document.getElementById(’result’).innerText = JSON.stringify(data, null, 2);
            })
            .catch(error => console.error(’Error:’, error));
    }
</script>