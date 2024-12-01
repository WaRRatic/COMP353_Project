function addGroupPermission() {
    const tableBody = document.querySelector('#group-permissions-table tbody');
    const row = document.createElement('tr');

    // Permission Level
    const permissionLevelCell = document.createElement('td');
    permissionLevelCell.textContent = 'group';
    row.appendChild(permissionLevelCell);

    // Authorized Member
    const authorizedGroupCell = document.createElement('td');
    const groupInput = document.createElement('input');
    groupInput.type = 'text';
    authorizedGroupCell.appendChild(groupInput);
    row.appendChild(authorizedGroupCell);

    // Permission Type
    const permissionTypeCell = document.createElement('td');
    const permissionTypeSelect = document.createElement('select');
    ['read', 'comment', 'share','link'].forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        option.text = type;
        permissionTypeSelect.add(option);
    });
    permissionTypeCell.appendChild(permissionTypeSelect);
    row.appendChild(permissionTypeCell);

    // Action
    const actionCell = document.createElement('td');
    const deleteButton = document.createElement('button');
    deleteButton.textContent = 'Remove';
    deleteButton.onclick = () => row.remove();
    actionCell.appendChild(deleteButton);
    row.appendChild(actionCell);

    tableBody.appendChild(row);
}