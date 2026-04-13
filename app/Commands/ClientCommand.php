<?php
namespace App\Commands;
use App\Interfaces\Persistable;
use App\utils\DateManager;
use App\utils\NumDocsHelper;

class ClientCommand implements Persistable
{
    private ?string $code = null;
    private ?string $zone = null;

    private ?string $firstName = null;
    private ?string $middleName = null;
    private ?string $lastName = null;
    private ?string $secondLastName = null;

    private ?string $numDoc = null;
    private ?string $direction = null;
    private ?string $neighborhood = null;
    private ?string $distric = null;
    private ?string $dpto = null;
    private ?string $dateRegister = null;
    private ?string $birthDate = null;

    private bool $isActive = false;

    private ?string $regimen = null;
    private ?string $maritalStatus = null;
    private ?string $userType = null;
    private ?string $ocupation = null;
    private ?string $eps = null;
    private ?string $covenat = null;
    private ?string $sex = null;
    private ?string $email = null;
    private ?string $phone = null;
    private ?string $typeDoc = null;
    private ?string $user = null;

    private ?bool $canSing = null;

    private ?string $guardianDocument = null;
    private ?string $guardianFirstName = null;
    private ?string $guardianMiddleName = null;
    private ?string $guardianLastName = null;
    private ?string $guardianSecondLastName = null;
    private ?string $guardianPhone = null;
    private ?string $guardianRelationship = null;

    private ?string $etnia = null;
    private ?string $populationGroup = null;
    private ?string $hasDissabled = null;
    private ?string $typeDissabled = null;
    private ?int $country=null;

    private ?string $rh = null;
    private ?string $placeBirth = null;
    private ?string $academicLevel = null;

    private ?int $numHijos = null;
    private bool $isNew;

    private ?string $urlImage=null;
    private ?string $urlDocument=null;
    private ?string $sisben=null;
    private ?string $observations=null;

    public function __construct(bool $isNew=false)
    {
        $this->isNew = $isNew;
    }


    public static function create(bool $isNew=false): self
    {
        return new self(isNew:$isNew);
    }

    public function getVerificationDigit():int{
        return NumDocsHelper::calculateDV($this->numDoc);
    }
    public function getAge():int{
        return DateManager::getYears($this->birthDate);
    }
    private function getMouths():int{
        return DateManager::getMonths($this->birthDate);
    }
    public function toPersistenceArray(): array
    {
        
        $data= [
            'codigo'=>$this->code,
            'zona'=>$this->zone,
            'nombre' =>
                $this->firstName.' '.
                ($this->middleName ?? '').' '.
                $this->lastName.' '.
                ($this->secondLastName ?? ''),
            'nit_cli'=>$this->numDoc,
            'direcc' =>$this->direction,
            'barrio'=>$this->neighborhood,
            'municipio'=>$this->distric,
            'depto'=>$this->dpto,
            'pais'=>$this->country,
            'sdt_f_nacio'=>$this->birthDate,
            'regim'=>'RÉGIMEN ORDINARIO',
            'activo'=>$this->isActive?'1':'0',
            'ok_ent'=>'0',
            'dv'=>$this->getVerificationDigit(),
            'ecivil'=>$this->maritalStatus,
            'carne'=>$this->numDoc,
            'tip_usuario'=>$this->userType,
            'contrib'=>$this->regimen,
            'ocupacion'=>$this->ocupation,
            'codent'=>$this->eps,
            'codent2'=>$this->covenat,
            'sexo' =>$this->sex,
            'dpto' =>(int)$this->dpto,
            'direcc1'=>$this->direction,
            'pn'=>$this->firstName,
            'sn'=>$this->middleName,
            'pa'=>$this->lastName,
            'sa'=>$this->secondLastName,
            'email'=>$this->email,
            'tip_iden'=>$this->typeDoc,
            'asesor'=>$this->phone,
            'años'=>$this->getAge(),
            'sabefirmar'=>$this->canSing?'1':'0',

            'telacompañante'=>$this->guardianPhone,
            'nombreresponsable' =>
                    $this->guardianFirstName.' '.
                    ($this->guardianMiddleName ?? '').' '.
                    $this->guardianLastName.' '.
                    ($this->guardianSecondLastName ?? ''),
            'parentresponsable'=>$this->guardianRelationship,

            'meses'=>$this->getMouths(),
            'cedula_resp'=>$this->guardianDocument,

            'G_poblacional'=>$this->populationGroup,
            'P_etnica'=>$this->etnia,
            'rh'=>$this->rh,
            'cel'=>$this->phone,
            'acompañante'=>
                    $this->guardianFirstName.' '.
                    ($this->guardianMiddleName ?? '').' '.
                    $this->guardianLastName.' '.
                    ($this->guardianSecondLastName ?? ''),
            'tel_acompa'=>$this->guardianPhone,
            'lugarnac'=>$this->placeBirth,
            'parentacompañante'=>$this->guardianRelationship,
            'cedulaacompañante'=>$this->guardianDocument,
            'niv_academi'=>$this->academicLevel,
            'n_hijos'=>$this->numHijos,
            'cod_ciudad'=>$this->distric,
            'zona1'=>$this->neighborhood,
                        
            'usumodi'=>$this->user,
            

        ];
        if ($this->isNew) {
            $data['usucrea'] = $this->user;
            $data['sdt_creado']=$this->dateRegister;
            $data['sdt_fechareg']=$this->dateRegister;
        }

        return $data;
    }
    public function toClient2Array():array{
        $data= [
            'codigo'=>$this->code,
            'audio'=>'0',
            'tip_discapacidad' => is_numeric($this->typeDissabled) ? (int)$this->typeDissabled : 0,
            'tipo_persona'=>'0',
            'email_facte'=>$this->email,
            'pn_responsable'=>$this->guardianFirstName??'',
            'sn_responsable'=>$this->guardianMiddleName??'',
            'pa_responsable'=>$this->guardianLastName??'',
            'sa_responsable'=>$this->guardianSecondLastName??'',
            'pais_origen'=>$this->country,
            'observaciones_asp'=>$this->observations,
            'grupo_sisben'=>$this->sisben
            


        ];
        if ($this->isNew) {

            $data['sdt_fec_asig_prog']=$this->dateRegister;
            $data['clinico_nuevo']='1';
        }

        return $data;
    }
    public function toNitsArray():array{
        $data= [
            't_doc'=>$this->typeDoc,
            'nit'=>$this->numDoc,
            'dv'=>$this->getVerificationDigit(),
            'nom1'=>$this->firstName,
            'nom2'=>$this->middleName,
            'ape1'=>$this->lastName,
            'ape2'=>$this->secondLastName,
            'nom_dir'=>$this->firstName.' '.($this->middleName ?? '').' '.$this->lastName.' '.($this->secondLastName ?? ''),
            'direcc'=>$this->direction,
            'barrio'=>$this->neighborhood,
            'ciudad'=>$this->distric,
            'telefono'=>$this->phone,
            'regimen'=>'SIMPLIFICADO',
            'pais'=>$this->country,
            'email'=>$this->email,
            'celular'=>$this->phone,
            'act_econ'=>$this->ocupation,
        ];
        if ($this->isNew) {
            $data['sdt_f_ingre']=$this->dateRegister;
        }
        return $data;
    }
    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setZone(string $zone): self
    {
        $this->zone = $zone;
        return $this;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;
        return $this;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function setSecondLastName(?string $secondLastName): self
    {
        $this->secondLastName = $secondLastName;
        return $this;
    }

    public function setNumDoc(string $numDoc): self
    {
        $this->numDoc = $numDoc;
        return $this;
    }

    public function setDirection(string $direction): self
    {
        $this->direction = $direction;
        return $this;
    }

    public function setNeighborhood(string $neighborhood): self
    {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    public function setDistric(string $distric): self
    {
        $this->distric = $distric;
        return $this;
    }

    public function setDpto(string $dpto): self
    {
        $this->dpto = $dpto;
        return $this;
    }

    public function setDateRegister(string $dateRegister): self
    {
        $this->dateRegister = $dateRegister;
        return $this;
    }

    public function setBirthDate(string $birthDate): self
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function setRegimen(string $regimen): self
    {
        $this->regimen = $regimen;
        return $this;
    }

    public function setMaritalStatus(string $maritalStatus): self
    {
        $this->maritalStatus = $maritalStatus;
        return $this;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;
        return $this;
    }

    public function setOcupation(string $ocupation): self
    {
        $this->ocupation = $ocupation;
        return $this;
    }

    public function setEps(string $eps): self
    {
        $this->eps = $eps;
        return $this;
    }

    public function setCovenat(string $covenat): self
    {
        $this->covenat = $covenat;
        return $this;
    }

    public function setSex(string $sex): self
    {
        $this->sex = $sex;
        return $this;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    public function setTypeDoc(string $typeDoc): self
    {
        $this->typeDoc = $typeDoc;
        return $this;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function setCanSing(bool $canSing): self
    {
        $this->canSing = $canSing;
        return $this;
    }

    public function setGuardianDocument(string $guardianDocument): self
    {
        $this->guardianDocument = $guardianDocument;
        return $this;
    }

    public function setGuardianFirstName(string $guardianFirstName): self
    {
        $this->guardianFirstName = $guardianFirstName;
        return $this;
    }

    public function setGuardianMiddleName(?string $guardianMiddleName): self
    {
        $this->guardianMiddleName = $guardianMiddleName;
        return $this;
    }

    public function setGuardianLastName(string $guardianLastName): self
    {
        $this->guardianLastName = $guardianLastName;
        return $this;
    }

    public function setGuardianSecondLastName(?string $guardianSecondLastName): self
    {
        $this->guardianSecondLastName = $guardianSecondLastName;
        return $this;
    }

    public function setGuardianPhone(string $guardianPhone): self
    {
        $this->guardianPhone = $guardianPhone;
        return $this;
    }

    public function setGuardianRelationship(string $guardianRelationship): self
    {
        $this->guardianRelationship = $guardianRelationship;
        return $this;
    }

    public function setEtnia(?string $etnia): self
    {
        $this->etnia = $etnia;
        return $this;
    }

    public function setPopulationGroup(?string $populationGroup): self
    {
        $this->populationGroup = $populationGroup;
        return $this;
    }

    public function setRh(string $rh): self
    {
        $this->rh = $rh;
        return $this;
    }

    public function setPlaceBirth(string $placeBirth): self
    {
        $this->placeBirth = $placeBirth;
        return $this;
    }

    public function setAcademicLevel(string $academicLevel): self
    {
        $this->academicLevel = $academicLevel;
        return $this;
    }

    public function setNumHijos(int $numHijos): self
    {
        $this->numHijos = $numHijos;
        return $this;
    }
    public function setHasDissabled(bool $disabled):self{
        $this->hasDissabled=$disabled;
        return $this;
    }
    public  function setTypeDissabled(?string $type):self{
        $this->typeDissabled=$type;
        return $this;
    }
    public function getNumDoc():string{
        return $this->numDoc;
    }
    public function getDocumentType():string{
        return $this->typeDoc;
    }
    public function setCountry(int $newCountry):self{
        $this->country = $newCountry;
        return $this;
    }
    public function getCode():?String{
        return $this->code;
    }
    public function setUrlPhoto(?string $url):self{
        $this->urlImage=$url;
        return $this;

    }
    public function setUrlDocument(string $url):self{
        $this->urlDocument=$url;
        return $this;

    }
    public function setSisben(?string $sisben):self{
        $this->sisben=$sisben;
        return $this;

    }
        public function setObserva(?string $observa):self{
        $this->observations=$observa;
        return $this;

    }
}