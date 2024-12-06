<?php
// Incluir archivo de conexión a la base de datos
include __DIR__ . '/../../src/database/db.php';

session_start();

// Si no hay una sesión activa, redirigir a /sesion/sesion.php
if (!isset($_SESSION['user_id'])) {
    header('Location: /sesion/sesion.php');  // Cambia esto por la URL de tu página de sesión
    exit();
}

$user_id = $_SESSION['user_id'];

// Si el formulario ha sido enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Recibimos los datos del formulario
    $productos = json_decode($_POST['productos'], true); // Array con los productos agregados
    $venta_tipo = $_POST['venta_tipo']; // Tipo de venta (local o tianguis)
    $total_amount = $_POST['total_amount']; // Total de la venta
    
    // 1. Registrar la venta
    $stmt = $pdo->prepare("INSERT INTO sales (user_id, total_amount, sale_type) VALUES (:user_id, :total_amount, :sale_type)");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':total_amount', $total_amount);
    $stmt->bindParam(':sale_type', $venta_tipo);
    $stmt->execute();
    $sale_id = $pdo->lastInsertId();  // Obtener el ID de la venta registrada
    
    $total_amount_calculado = 0;

    // 2. Registrar los detalles de la venta y actualizar el stock
    foreach ($productos as $producto) {
        // Calcular el total por producto
        $total_price = $producto['price'] * $producto['quantity'];
        $total_amount_calculado += $total_price;
        
        // Insertar los detalles de la venta
        $stmt = $pdo->prepare("INSERT INTO sale_details (sale_id, product_id, size_id, color_id, quantity, price, total_price) 
                               VALUES (:sale_id, :product_id, :size_id, :color_id, :quantity, :price, :total_price)");
        $stmt->bindParam(':sale_id', $sale_id);
        $stmt->bindParam(':product_id', $producto['id']);
        $stmt->bindParam(':size_id', $producto['size_id']);
        $stmt->bindParam(':color_id', $producto['color_id']);
        $stmt->bindParam(':quantity', $producto['quantity']);
        $stmt->bindParam(':price', $producto['price']);
        $stmt->bindParam(':total_price', $total_price);
        $stmt->execute();

        // Actualizar el stock según el tipo de venta
        $stock_column = ($venta_tipo == 'local') ? 'stock_local' : 'stock_tianguis';
        
        $stmt = $pdo->prepare("UPDATE shoes_variations SET $stock_column = $stock_column - :quantity 
                               WHERE id_shoe = :product_id AND id_size = :size_id AND id_color = :color_id");
        $stmt->bindParam(':quantity', $producto['quantity']);
        $stmt->bindParam(':product_id', $producto['id']);
        $stmt->bindParam(':size_id', $producto['size_id']);
        $stmt->bindParam(':color_id', $producto['color_id']);
        $stmt->execute();
    }

    // Actualizar el total de la venta
    $stmt = $pdo->prepare("UPDATE sales SET total_amount = :total_amount WHERE id_sale = :sale_id");
    $stmt->bindParam(':total_amount', $total_amount_calculado);
    $stmt->bindParam(':sale_id', $sale_id);
    $stmt->execute();

    // Confirmación de la venta
    echo "Venta registrada exitosamente. Total: $" . $total_amount_calculado;
} else {
    // Si no se ha enviado el formulario, mostrar el formulario de ventas.
    // Puedes agregar tu formulario aquí, como ya lo tienes en el código anterior
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Punto de Venta</h1>
    
    <form id="form_venta" method="POST">
        <div>
            <label for="venta_tipo">Tipo de Venta:</label>
            <select id="venta_tipo" name="venta_tipo" required>
                <option value="local">Stock Local</option>
                <option value="tianguis">Stock Tianguis</option>
            </select>
        </div>

        <!-- Modal para seleccionar productos -->
        <button type="button" id="btn_modal" onclick="abrirModal()">Seleccionar Producto</button>
        
        <!-- Lista de productos seleccionados -->
        <table id="productos_lista">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Talla</th>
                    <th>Color</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Productos agregados aparecerán aquí -->
            </tbody>
        </table>
        
        <!-- Total de la venta -->
        <div>
            <label for="total_venta">Total de la Venta:</label>
            <span id="total_venta">$0.00</span>
        </div>

        <button type="submit">Proceder al Pago</button>
    </form>

    <!-- Modal de productos -->
    <div id="modal" style="display:none;">
        <div id="modal_contenido">
            <h2>Seleccionar Producto</h2>
            <input type="text" id="buscador_producto" placeholder="Buscar por nombre" onkeyup="buscarProducto()">
            <table id="tabla_productos">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Seleccionar</th>
                    </tr>
                </thead>
                <tbody id="productos_encontrados">
                    <!-- Los productos serán cargados aquí -->
                </tbody>
            </table>
            <button type="button" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>

    <script>
        // Variables
        let productosSeleccionados = [];

        // Función para abrir el modal
        function abrirModal() {
            $('#modal').show();
            cargarProductos(); // Cargar productos cuando el modal se abre
        }

        // Función para cerrar el modal
        function cerrarModal() {
            $('#modal').hide();
        }

        // Función para buscar productos
        function buscarProducto() {
            var query = $('#buscador_producto').val().toLowerCase();
            var productos = <?php
                // Obtener los productos desde la base de datos
                $stmt = $pdo->query("SELECT id_shoe, name FROM shoes");
                $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode($productos);
            ?>;
            var resultados = productos.filter(p => p.name.toLowerCase().includes(query));
            mostrarProductos(resultados);
        }

        // Función para mostrar los productos en el modal
        function mostrarProductos(productos) {
            var tbody = $('#productos_encontrados');
            tbody.empty();
            productos.forEach(producto => {
                tbody.append(`
                    <tr>
                        <td>${producto.name}</td>
                        <td><button onclick="seleccionarProducto(${producto.id_shoe})">Seleccionar</button></td>
                    </tr>
                `);
            });
        }

        // Función para seleccionar un producto
        function seleccionarProducto(idProducto) {
            $.get('get_product_info.php', { id_shoe: idProducto }, function(producto) {
                // Agregar el producto a la lista de productos
                productosSeleccionados.push(producto);
                actualizarLista();
                cerrarModal();
            });
        }

        // Función para actualizar la lista de productos
        function actualizarLista() {
            var tbody = $('#productos_lista tbody');
            tbody.empty();
            var totalVenta = 0;
            productosSeleccionados.forEach(producto => {
                var totalProducto = producto.price * producto.quantity;
                tbody.append(`
                    <tr>
                        <td>${producto.name}</td>
                        <td>${producto.size}</td>
                        <td>${producto.color}</td>
                        <td>$${producto.price}</td>
                        <td>${producto.quantity}</td>
                        <td>$${totalProducto}</td>
                    </tr>
                `);
                totalVenta += totalProducto;
            });
            $('#total_venta').text('$' + totalVenta.toFixed(2));
        }

        // Enviar la venta
        $('#form_venta').submit(function(event) {
            event.preventDefault();

        // Preparar los datos para enviar
        var productos = productosSeleccionados.map(p => ({
            id: p.id,
            size_id: p.size_id,
            color_id: p.color_id,
            quantity: p.quantity,
            price: p.price
        }));

        $.post('punto_de_venta.php', {
            productos: JSON.stringify(productos),
            venta_tipo: $('#venta_tipo').val(),
            total_amount: $('#total_venta').text().replace('$', '')
        }, function(response) {
            alert(response);
            // Aquí puedes agregar lógica para mostrar el ticket o limpiar la lista
        });
    });
</script>
</body>
</html>
