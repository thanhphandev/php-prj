### 1. **In giá trị của biến**
- **Cú pháp**: `<?= $variable ?>`
- **Giải thích**: In giá trị của biến vào HTML.
  ```php
  <h1><?= $title ?></h1>
  ```

### 2. **Câu lệnh điều kiện `if-else`**
- **Cú pháp**:
  ```php
  <?php if (condition): ?>
      // Nội dung nếu điều kiện đúng
  <?php else: ?>
      // Nội dung nếu điều kiện sai
  <?php endif; ?>
  ```
- **Giải thích**: Kiểm tra điều kiện và hiển thị nội dung tương ứng.
  ```php
  <?php if ($isLoggedIn): ?>
      <h1>Chào mừng bạn quay lại!</h1>
  <?php else: ?>
      <h1>Chào mừng bạn đến với trang web của chúng tôi!</h1>
  <?php endif; ?>
  ```

### 3. **Toán tử điều kiện ba phần `ternary operator`**
- **Cú pháp**:
  ```php
  condition ? value_if_true : value_if_false;
  ```
- **Giải thích**: Kiểm tra điều kiện và trả về giá trị tùy vào điều kiện.
  ```php
  <h1><?= $isLoggedIn ? 'Chào mừng bạn quay lại!' : 'Chào mừng bạn đến với trang web của chúng tôi!' ?></h1>
  ```

### 4. **Vòng lặp `foreach`**
- **Cú pháp**:
  ```php
  <?php foreach ($array as $item): ?>
      // Nội dung lặp qua mảng
  <?php endforeach; ?>
  ```
- **Giải thích**: Duyệt qua mảng và hiển thị mỗi phần tử.
  ```php
  <ul>
      <?php foreach ($menuItems as $item): ?>
          <li><a href="#"><?= $item ?></a></li>
      <?php endforeach; ?>
  </ul>
  ```

### 5. **Điều kiện trong vòng lặp `foreach`**
- **Cú pháp**:
  ```php
  <?php foreach ($array as $item): ?>
      <?php if (condition): ?>
          // Nội dung nếu điều kiện đúng
      <?php endif; ?>
  <?php endforeach; ?>
  ```
- **Giải thích**: Kết hợp `if` với `foreach` để kiểm tra từng phần tử trong mảng.
  ```php
  <ul>
      <?php foreach ($posts as $post): ?>
          <?php if ($post['author'] == 'Người viết A'): ?>
              <li><?= $post['title'] ?> (<?= $post['author'] ?>)</li>
          <?php endif; ?>
      <?php endforeach; ?>
  </ul>
  ```

### 6. **Câu lệnh `switch-case`**
- **Cú pháp**:
  ```php
  <?php switch ($variable): ?>
      <?php case 'value1': ?>
          // Nội dung nếu bằng value1
      <?php break; ?>
      <?php case 'value2': ?>
          // Nội dung nếu bằng value2
      <?php break; ?>
      <?php default: ?>
          // Nội dung nếu không có giá trị nào khớp
  <?php endswitch; ?>
  ```
- **Giải thích**: Kiểm tra giá trị của một biến với các trường hợp cụ thể.
  ```php
  <?php switch ($role): ?>
      <?php case 'admin': ?>
          <h1>Chào admin</h1>
      <?php break; ?>
      <?php case 'user': ?>
          <h1>Chào người dùng</h1>
      <?php break; ?>
      <?php default: ?>
          <h1>Chào khách</h1>
  <?php endswitch; ?>
  ```

### 7. **Vòng lặp `for`**
- **Cú pháp**:
  ```php
  <?php for ($i = 0; $i < 10; $i++): ?>
      // Nội dung lặp qua chỉ số
  <?php endfor; ?>
  ```
- **Giải thích**: Duyệt từ 0 đến 9, in ra nội dung tương ứng.
  ```php
  <ul>
      <?php for ($i = 0; $i < 5; $i++): ?>
          <li>Item <?= $i ?></li>
      <?php endfor; ?>
  </ul>
  ```

### 8. **Vòng lặp `while`**
- **Cú pháp**:
  ```php
  <?php while (condition): ?>
      // Nội dung lặp khi điều kiện đúng
  <?php endwhile; ?>
  ```
- **Giải thích**: Lặp cho đến khi điều kiện sai.
  ```php
  <?php $i = 0; ?>
  <ul>
      <?php while ($i < 5): ?>
          <li>Item <?= $i ?></li>
          <?php $i++; ?>
      <?php endwhile; ?>
  </ul>
  ```

### 9. **Sử dụng include/require để chèn file**
- **Cú pháp**:
  ```php
  <?php include 'header.php'; ?>
  ```
  Hoặc
  ```php
  <?php require 'footer.php'; ?>
  ```
- **Giải thích**: Chèn file PHP vào trong một file khác.

### 10. **Sử dụng hàm `echo` hoặc `print`**
- **Cú pháp**:
  ```php
  <?php echo "Hello, World!"; ?>
  ```
  Hoặc
  ```php
  <?php print "Hello, World!"; ?>
  ```
- **Giải thích**: Dùng để in chuỗi hoặc giá trị ra màn hình.

### 11. **In mảng với `print_r`**
- **Cú pháp**:
  ```php
  <pre><?php print_r($array); ?></pre>
  ```
- **Giải thích**: In mảng một cách dễ đọc.

### 12. **Sử dụng `isset` để kiểm tra biến**
- **Cú pháp**:
  ```php
  <?php if (isset($variable)): ?>
      // Nội dung nếu biến được khai báo
  <?php endif; ?>
  ```
- **Giải thích**: Kiểm tra xem biến có tồn tại hay không trước khi sử dụng.

### 13. **Sử dụng `empty` để kiểm tra giá trị rỗng**
- **Cú pháp**:
  ```php
  <?php if (empty($variable)): ?>
      // Nội dung nếu biến rỗng
  <?php endif; ?>
  ```
- **Giải thích**: Kiểm tra nếu biến không có giá trị hoặc là null.

### Tóm tắt:

- **In giá trị**: `<?= $variable ?>`
- **Điều kiện `if-else`**: `<?php if (condition): ?> ... <?php else: ?> ... <?php endif; ?>`
- **Toán tử điều kiện**: `condition ? value_if_true : value_if_false`
- **Vòng lặp `foreach`**: `<?php foreach ($array as $item): ?> ... <?php endforeach; ?>`
- **Điều kiện trong vòng lặp**: Kết hợp `if` với `foreach`
- **Câu lệnh `switch-case`**: `<?php switch ($variable): ?> ... <?php endswitch; ?>`
- **Vòng lặp `for`**: `<?php for ($i = 0; $i < 10; $i++): ?> ... <?php endfor; ?>`
- **Vòng lặp `while`**: `<?php while (condition): ?> ... <?php endwhile; ?>`
- **Chèn file**: `<?php include 'file.php'; ?>`
- **In chuỗi**: `<?php echo "text"; ?>`
- **In mảng**: `<?php print_r($array); ?>`
- **Kiểm tra biến**: `<?php if (isset($variable)): ?> ... <?php endif; ?>`
- **Kiểm tra rỗng**: `<?php if (empty($variable)): ?> ... <?php endif; ?>`
