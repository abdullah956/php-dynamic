<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Dropdowns</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/i18n/zh-CN.min.js"></script>
</head>

<body>
    <button name="btn" id="add">Add</button><br>

    <div id="dropdownContainer">
        <?php
        $conn = new mysqli('localhost', 'root', '', 'dynamic');
        $query = "SELECT * FROM products";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            echo '<span class="counter">Dropdowns: 1</span><br>';
            echo '<div class="dropdown-container" data-index="1">';
            echo '<select name="selected_item[]" class="dropdown" onchange="hello();"><option value="" disabled selected>Select item</option>';
            while ($row = $result->fetch_assoc()) {
                $i = $row['id'];
                $n = $row['name'];
                $p = $row['price'];
                echo "<option value=\"$p\">$n</option>";
            }
            echo '</select>';
            echo '<input type="number" class="quantity" value="1" min="1" onchange="hello();">'; // Quantity input
            echo '<span class="response"></span>';
            echo '<button class="remove" onclick="removeDropdown(this)">Remove</button><br>';
            echo '</div>';
        } else {
            echo "No records found";
        }
        $conn->close();
        ?>
    </div>

    <script>
        function hello() {
            var selected = $(".dropdown");
            var quantities = $(".quantity");
            var responses = $(".response");
            var totalProduct = 0;
            var dropdownCount = selected.length;

            selected.each(function (i) {
                var selectedIndex = this.selectedIndex;
                var selectedOption = this.options[selectedIndex];
                var price = parseFloat(selectedOption.value);
                var quantity = parseInt(quantities.eq(i).val());
                var subtotal = price * quantity;
                responses.eq(i).text("Subtotal: " + subtotal.toFixed(2));
                totalProduct += subtotal;
            });

            $("#product").text("Total Product: " + totalProduct.toFixed(2));
            $(".counter").text("Dropdowns: " + dropdownCount);
        }

        function removeDropdown(button) {
            $(button).parent().remove();
            hello();
        }

        $(document).ready(function () {
            var index = 1;

            $("#add").click(function () {
                index++;
                var newDropdownContainer = $('<div class="dropdown-container" data-index="' + index + '">');
                newDropdownContainer.append('<select name="selected_item[]" class="dropdown" onchange="hello();"><option value="" disabled selected>Select item</option></select>');
                
                // Fetch options and append them to the new dropdown
                $.ajax({
                    url: "fetch_options.php",
                    type: "GET",
                    success: function (data) {
                        newDropdownContainer.find(".dropdown").html(data);

                        newDropdownContainer.append('<input type="number" class="quantity" value="1" min="1" onchange="hello();">');
                        newDropdownContainer.append('<span class="response"></span>');
                        newDropdownContainer.append('<button class="remove" onclick="removeDropdown(this)">Remove</button><br>');

                        // Append the new dropdown container to the dropdownContainer
                        $("#dropdownContainer").append(newDropdownContainer);

                        // Initialize Select2 for the new dropdown
                        newDropdownContainer.find(".dropdown").select2();

                        hello();
                    },
                    error: function (xhr, status, error) {
                        console.error("Error fetching options:", status, error);
                    }
                });
            });

            // Initialize Select2 for the initial dropdown
            $(".dropdown").select2();
        });
    </script>

    <span id="product">Total Product: 0.00</span>
</body>

</html>

