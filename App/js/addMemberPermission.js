function addMemberPermission() {
    const tableBody = document.querySelector('#member-permissions-table tbody');
    const row = document.createElement('tr');

    // Permission Level
    const permissionLevelCell = document.createElement('td');
    permissionLevelCell.textContent = 'member';
    row.appendChild(permissionLevelCell);

    // Authorized Member
    const authorizedMemberCell = document.createElement('td');
    const memberInput = document.createElement('input');
    memberInput.type = 'text';
    authorizedMemberCell.appendChild(memberInput);
    row.appendChild(authorizedMemberCell);

    // Permission Type
    const permissionTypeCell = document.createElement('td');
    const permissionTypeSelect = document.createElement('select');
    ['read', 'edit', 'comment', 'share','link'].forEach(type => {
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