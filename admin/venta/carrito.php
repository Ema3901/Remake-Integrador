<!-- Mostrar carrito -->
<h3>Carrito</h3>
<ul class="list-group">
    <?php if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])): ?>
        <?php
            $total_price = 0;
            foreach ($_SESSION['carrito'] as $item):
                $detalles = obtenerDetallesCarrito($item['id_variation']);
                $total_price += $detalles['price'];
        ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong><?php echo $detalles['model_name']; ?></strong> - 
                    <em><?php echo $detalles['sizeMX']; ?> | <?php echo $detalles['color']; ?></em>
                </div>
                <div class="text-end">
                    <span>$<?php echo $detalles['price']; ?></span>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="id_variation" value="<?php echo $item['id_variation']; ?>">
                        <button type="submit" name="eliminar_del_carrito" class="btn btn-danger btn-sm ms-2">Eliminar</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    <?php else: ?>
        <li class="list-group-item">No hay productos en el carrito.</li>
    <?php endif; ?>
</ul>

<!-- Total y botón de pago -->
<div class="d-flex justify-content-between mt-4">
    <h4>Total: $<?php echo number_format($total_price, 2); ?></h4>
    <form method="POST">
        <button type="submit" name="pagar" class="btn btn-success">Pagar</button>
    </form>
</div>
