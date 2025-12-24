<!DOCTYPE html>
<html>
<head>
  <title>Skin Analyzer</title>
  <style>
    video, canvas {
      width: 320px;
      height: 240px;
      border: 1px solid #ccc;
      margin-bottom: 10px;
    }
    #camera-section, #upload-section {
      margin-bottom: 30px;
    }
  </style>
</head>
<body>
  <h2>Skin Analyzer</h2>

  <!-- === Upload Option === -->
  <div id="upload-section">
    <h3>Option 1: Upload an Image</h3>
    <form action="analyzee_skin.php" method="POST" enctype="multipart/form-data">
      <input type="file" name="skin_image" accept="image/*" required>
      <button type="submit">Analyze Skin</button>
    </form>
  </div>

  <!-- === Camera Option === -->
  <div id="camera-section">
    <h3>Option 2: Use Live Camera</h3>

    <video id="video" autoplay></video><br>
    <button id="capture">Capture Photo</button><br><br>

    <canvas id="canvas" style="display:none;"></canvas>

    <form id="photoForm" action="analyzee_skin.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="image_data" id="image_data">
      <button type="submit">Analyze Captured Photo</button>
    </form>
  </div>

  <script>
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureButton = document.getElementById('capture');
    const imageDataInput = document.getElementById('image_data');

    // Ask for camera access
    navigator.mediaDevices.getUserMedia({ video: true })
      .then((stream) => {
        video.srcObject = stream;
      })
      .catch((err) => {
        alert("Camera access denied: " + err);
      });

    // Capture photo
    captureButton.addEventListener('click', () => {
      const context = canvas.getContext('2d');
      canvas.width = video.videoWidth;
      canvas.height = video.videoHeight;
      context.drawImage(video, 0, 0, canvas.width, canvas.height);

      // Convert to base64
      const dataURL = canvas.toDataURL('image/jpeg');
      imageDataInput.value = dataURL;
      alert("Photo captured! Now click 'Analyze Captured Photo'.");
    });
  </script>
</body>
</html>