<?php
$leaves = [
  'casual' => ['total' => 7, 'used' => 2],
  'sick'   => ['total' => 7, 'used' => 2],
  'earned' => ['total' => 12, 'used' => 6],
  'unpaid' => ['total' => 6, 'used' => 6],
  'half'   => ['total' => 6, 'used' => 6],
];

$colors = [
  'casual' => '#8b3ee5',
  'sick'   => '#2ec2f9',
  'earned' => '#7ed859',
  'unpaid' => '#ff2e61',
  'half'   => '#fcb63a'
];

foreach ($leaves as $key => $leave) {
    $used = $leave['used'];
    $total = $leave['total'];
    $available = $total - $used;
    $percent = ($used / $total) * 100;    
    $radius = 16;
    $circumference = 2 * pi() * $radius;
    $strokeDashoffset = $circumference - ($percent / 100 * $circumference);
    $strokeWidth = round($strokeDashoffset * $circumference);
    echo "
    <article class='leave-card $key'>
      <div class='title'>" . ucfirst($key) . " Leave</div>
      <div class='circle-bg'>
        <svg width='48' height='48' viewBox='0 0 36 36'>
          <!-- Background circle -->
          <circle cx='18' cy='18' r='16' class='bg' />
          <!-- Dynamic progress circle -->
          <circle class='progress' cx='18' cy='18' r='16'
            stroke-dasharray='$circumference'
            stroke-dashoffset='$strokeDashoffset' />
        </svg>
        
        <!-- Display percentage -->
        <div class='percent' style='color:{$colors[$key]};'>" . round($percent) . "%</div>
      </div>
      
      <div class='details'>
        <span>Available - $available</span>
        <span>Used - $used</span>
      </div>
    </article>
    ";
}
?>