let url = 'http://localhost:8000';


url = 'http://localhost:8000/drivers';
url = 'http://localhost:8000/drivers/661ea887a14732a15f0d7d02';



function onStreamRead(data) {
  data = JSON.parse(data);
  console.log('onStreamRead', data);
}

// Fetch the original image
fetch(url)
  // Retrieve its body as ReadableStream
  .then((response) => {
    const reader = response.body?.getReader();
    let latestMessage = null; 
    if (reader) {
      const read = async () => {
        try {
          const { done, value } = await reader.read();
          if (done) {
            reader.releaseLock(); // Release the reader's lock when done
            return;
          }
          
          const decoder = new TextDecoder('utf-8');
          const decodedValue = decoder.decode(value);
          const stringifiedValue = JSON.stringify(decodedValue);
          const data = JSON.parse(stringifiedValue);
          latestMessage = data;
          onStreamRead(data);
          // Continue reading data from the stream
          read();
        } catch (error) {
          console.error('Error reading stream:', error);
          reader.releaseLock(); // Release the reader's lock in case of error
        }
      };
      read(); // Start reading data from the stream
    }
  })
  // Create a new response out of the stream
  .then((response) => new Response(response))
  // Retrieve its body as ReadableStream
  .catch((error) => console.error('Error fetching stream:', error));
