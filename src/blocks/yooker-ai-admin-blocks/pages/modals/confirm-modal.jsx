import { Modal, Button, __experimentalText as Text, __experimentalGrid as Grid } from '@wordpress/components';
import {useState, useEffect} from 'react';

const ConfirmModal = ({ isOpen, onClose, type, object }) => {
    if (!isOpen) return null; 
    const [filteredObject, setFilteredObject] = useState();

    const confirm = () => {
        onClose(true); 
    };

    useEffect(() => {
        if (type == 'gebruiker'){
            setFilteredObject(object.company);
        }
        else if (type == 'abonnement'){
            setFilteredObject(object.name);
        }
        else {
            console.log('Something went wrong');
            onClose(false);
        }
    }, [])

    const cancel = () => {
        onClose(false);
    };

    return (
        <Modal onRequestClose={cancel} title="Confirm Deletion">
            <Text>Wil je zeker {type}: {filteredObject} verwijderen?</Text>
            <Grid columns={2}>
                <Button onClick={confirm} variant='primary'>
                    Ja
                </Button>
                <Button isDestructive variant='secondary' onClick={cancel}>
                    Nee
                </Button>
            </Grid>
        </Modal>
    );
};

export default ConfirmModal;
