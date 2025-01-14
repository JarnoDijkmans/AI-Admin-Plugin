import {useState} from 'react';
import {
    __experimentalGrid as Grid,
    Button,
    TextControl,
    Modal
} from '@wordpress/components';
import UserService from '../../services/user-service';

const CreateUserModal = ({isOpen, onClose, refreshUsers}) => {
    if (!isOpen) return null;

    const [user, setUser] = useState({
        id: '',
        company: '',
        email: '',
        address: '',
        town: '',
        zipcode: '',
        phonenumber: '',
        apiKey: '',
        first_name: '',
        last_name: '',
        surnameprefix: '',
        subscriptions: [],
    });

    const handleChange = (field, value) => {
        setUser((prev) => ({
            ...prev,
            [field]: value,
        }));
    };


    const handleSave = () => {
        UserService.saveUser(user)
            .then((result) => {
                if (result.success) {
                    onClose();
                    refreshUsers();
                } else {
                    console.error('Error saving user', result.message);
                }
            }).catch((error) => {
                console.error('Error', error);
            })
    };

    return (
        <Modal onRequestClose={onClose} shouldCloseOnClickOutside={false} shouldCloseOnEsc={true}>
            <Grid columns={3}>
                <TextControl
                    label="Voornaam"
                    value={user.first_name}
                    onChange={(value) => handleChange('first_name', value)}
                />
                <TextControl
                    label="Tussenvoegsel"
                    value={user.surnameprefix}
                    onChange={(value) => handleChange('surnameprefix', value)}
                />
                <TextControl
                    label="Achternaam"
                    value={user.last_name}
                    onChange={(value) => handleChange('last_name', value)}
                />
            </Grid>
    
            <Grid columns={1}>
                <TextControl
                    label="Bedrijf"
                    value={user.company}
                    onChange={(value) => handleChange('company', value)}
                />
                <TextControl
                    label="e-mail adres"
                    value={user.email}
                    onChange={(value) => handleChange('email', value)}
                    type="email"
                />
                <TextControl
                    label="Adres"
                    value={user.address}
                    onChange={(value) => handleChange('address', value)}
                />
            </Grid>
    
            <Grid columns={2}>
                <TextControl
                    label="Stad/Dorp"
                    value={user.town}
                    onChange={(value) => handleChange('town', value)}
                />
                <TextControl
                    label="Postcode"
                    value={user.zipcode}
                    onChange={(value) => handleChange('zipcode', value)}
                    help='Bijvoorbeeld: 1234AB'
                />
            </Grid>
    
            <Grid columns={1}>
                <TextControl
                    label="Telefoon nummer"
                    value={user.phonenumber}
                    onChange={(value) => handleChange('phonenumber', value)}
                    type="tel"
                />
            </Grid>
            <Button variant="primary" onClick={handleSave}>
                Maak nieuw account aan
            </Button>
        </Modal>
    );
}

export default CreateUserModal;