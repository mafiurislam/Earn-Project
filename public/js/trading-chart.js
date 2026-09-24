/* Animated Trading Growth Canvas Chart for Rajdoot Nivedan Media Customer Dashboard */

document.addEventListener('DOMContentLoaded', () => {
  const canvas = document.getElementById('tradingChart');
  if (!canvas) return;

  const ctx = canvas.getContext('2d');
  let animationFrameId;

  function resizeCanvas() {
    canvas.width = canvas.parentElement.clientWidth;
    canvas.height = canvas.parentElement.clientHeight || 220;
  }

  window.addEventListener('resize', resizeCanvas);
  resizeCanvas();

  // Control points for upward trend line
  const basePoints = [
    { x: 0.0, y: 0.75 },
    { x: 0.15, y: 0.65 },
    { x: 0.3, y: 0.70 },
    { x: 0.45, y: 0.50 },
    { x: 0.6, y: 0.55 },
    { x: 0.75, y: 0.30 },
    { x: 0.9, y: 0.25 },
    { x: 1.0, y: 0.15 }
  ];

  let time = 0;

  function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    time += 0.03;

    const width = canvas.width;
    const height = canvas.height;

    // Draw grid background
    ctx.strokeStyle = 'rgba(255, 255, 255, 0.05)';
    ctx.lineWidth = 1;
    const gridCols = 10;
    const gridRows = 5;

    for (let i = 0; i <= gridCols; i++) {
      const x = (width / gridCols) * i;
      ctx.beginPath();
      ctx.moveTo(x, 0);
      ctx.lineTo(x, height);
      ctx.stroke();
    }

    for (let j = 0; j <= gridRows; j++) {
      const y = (height / gridRows) * j;
      ctx.beginPath();
      ctx.moveTo(0, y);
      ctx.lineTo(width, y);
      ctx.stroke();
    }

    // Dynamic points calculation with subtle sine animation
    const animatedPoints = basePoints.map((pt, idx) => {
      const wave = Math.sin(time + idx) * 0.03;
      return {
        x: pt.x * width,
        y: (pt.y + wave) * height
      };
    });

    // Create Path
    ctx.beginPath();
    ctx.moveTo(animatedPoints[0].x, animatedPoints[0].y);

    for (let i = 0; i < animatedPoints.length - 1; i++) {
      const xc = (animatedPoints[i].x + animatedPoints[i + 1].x) / 2;
      const yc = (animatedPoints[i].y + animatedPoints[i + 1].y) / 2;
      ctx.quadraticCurveTo(animatedPoints[i].x, animatedPoints[i].y, xc, yc);
    }
    ctx.lineTo(animatedPoints[animatedPoints.length - 1].x, animatedPoints[animatedPoints.length - 1].y);

    // Gradient Fill
    const fillGradient = ctx.createLinearGradient(0, 0, 0, height);
    fillGradient.addColorStop(0, 'rgba(0, 200, 83, 0.35)');
    fillGradient.addColorStop(0.5, 'rgba(0, 200, 83, 0.12)');
    fillGradient.addColorStop(1, 'rgba(0, 200, 83, 0.0)');

    const fillPath = new Path2D();
    fillPath.moveTo(animatedPoints[0].x, height);
    fillPath.lineTo(animatedPoints[0].x, animatedPoints[0].y);

    for (let i = 0; i < animatedPoints.length - 1; i++) {
      const xc = (animatedPoints[i].x + animatedPoints[i + 1].x) / 2;
      const yc = (animatedPoints[i].y + animatedPoints[i + 1].y) / 2;
      fillPath.quadraticCurveTo(animatedPoints[i].x, animatedPoints[i].y, xc, yc);
    }
    fillPath.lineTo(animatedPoints[animatedPoints.length - 1].x, animatedPoints[animatedPoints.length - 1].y);
    fillPath.lineTo(width, height);
    fillPath.closePath();

    ctx.fillStyle = fillGradient;
    ctx.fill(fillPath);

    // Stroke Line
    const strokeGradient = ctx.createLinearGradient(0, 0, width, 0);
    strokeGradient.addColorStop(0, '#00C853');
    strokeGradient.addColorStop(0.7, '#00E676');
    strokeGradient.addColorStop(1, '#FF007F');

    ctx.strokeStyle = strokeGradient;
    ctx.lineWidth = 3.5;
    ctx.shadowColor = 'rgba(0, 230, 118, 0.6)';
    ctx.shadowBlur = 12;
    ctx.stroke();

    // Reset shadow
    ctx.shadowBlur = 0;

    // Draw glowing data points
    animatedPoints.forEach((pt, index) => {
      ctx.beginPath();
      ctx.arc(pt.x, pt.y, 4, 0, Math.PI * 2);
      ctx.fillStyle = index === animatedPoints.length - 1 ? '#FF007F' : '#00E676';
      ctx.fill();

      // Outer pulse on the highest right node
      if (index === animatedPoints.length - 1) {
        ctx.beginPath();
        const pulseR = 6 + Math.sin(time * 3) * 4;
        ctx.arc(pt.x, pt.y, pulseR, 0, Math.PI * 2);
        ctx.strokeStyle = 'rgba(255, 0, 127, 0.6)';
        ctx.lineWidth = 2;
        ctx.stroke();
      }
    });

    animationFrameId = requestAnimationFrame(draw);
  }

  draw();
});
