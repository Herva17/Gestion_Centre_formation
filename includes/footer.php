            </div>
        </div>
    </div>

    <script>
        // Alert dismissal
        document.querySelectorAll('[data-dismiss="alert"]').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.alert').remove();
            });
        });

        // Form validation
        function validateForm() {
            const inputs = document.querySelectorAll('input[required], textarea[required], select[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            return isValid;
        }

        // Table search
        function searchTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tr');

            input.addEventListener('keyup', function() {
                const filter = input.value.toUpperCase();
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const cells = row.getElementsByTagName('td');
                    let found = false;
                    
                    for (let j = 0; j < cells.length; j++) {
                        if (cells[j].textContent.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                    
                    row.style.display = found ? '' : 'none';
                }
            });
        }

        // Format currency
        function formatCurrency(value) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'currency',
                currency: 'XOF'
            }).format(value);
        }

        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
