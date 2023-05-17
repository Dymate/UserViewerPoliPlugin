<?php
import("plugins.generic.userViewerPoliPlugin.controllers.UsersListTableHandler");
import("plugins.generic.userViewerPoliPlugin.controllers.util.UsersListTableHandlerComplements");
class GenerateUsersTable{
    public function generateUsersListHandler(){
        return new UsersListTableHandler();
    }
    public function generateUsersListHandlerComplement(){
        return new UsersListTableHandlerComplements();
    }
    public function __construct(){
    }

    public function listUsers($data,$roles)
    {
        $userListComplements=$this->generateUsersListHandlerComplement();
        $table = '';
        foreach ($data as $row) {
            $table .= '
                <tr>
                
                  <td>   
                    <input type="checkbox" name="exportUser[]" value="'.$row->getUserId().'" onclick="updateCheckboxValues(this.value)">
                  </td>

                  <td>' . $row->getFirstName() . '</td>
                  <td>' . $row->getLastName() . '</td>
                  <td>' . $row->getCountry() . '</td>';
                 if(!$roles){
                 $table.= '<td>' . $userListComplements->translateRolesIdToText($row->getRoles()) . '</td>';
                }else{
                  $table.= '<td>         </td>';
                }
                  $table.='<td>
                      <button class="btn btn-warning"type="button" data-toggle="modal" data-target="#myModal' . $row->getUserId() . '">
                          <i class="glyphicon glyphicon-eye-open" style="color: black"></i>
                          
                      </button>&nbsp;
                  </td>
                </tr>';
            $table = $this->generateModalWindow($row, $table);
        }
        return $table;
    }
    public function generateModalWindow($user, $table)
    {   $userListComplements=$this->generateUsersListHandlerComplement();
        $userListHandler=$this->generateUsersListHandler();
        $table .= '
<div class="modal fade" id="myModal' . $user->getUserId() . '" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content ">
            <!-- Encabezado de la ventana modal -->
            <div class="modal-header" style="height:70px">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="h1" style="">Información del usuario</div>
                        </div>

                        <div class="col-sm-3"><button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Cuerpo de la ventana modal -->
            <div class="modal-body">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="h4"> ID:' . $user->getUserId() . '</div>
                            <div class="h3">' . $user->getFirstName() . ' ' . $user->getLastName() . '</div>
                           
                        </div>
                        <div class="col-sm-6">
                            <div><b>Usuario: </b>' . $user->getUserName() . '</div>
                            <div><b>Email: </b>' . $user->getEmail() . '</div>
                            <div><b>Nacionalidad: </b>' . $user->getCountry() . '</div>
                            <div><b>Universidad: </b> ' . $user->getUniversity() . ' <button class="text-success" style="border:none; background:none" type="button" data-toggle="modal" data-target="#mySecondModal' . $user->getUserId() . '">
                            <i class="text-success glyphicon glyphicon-pencil" ></i>
                            </button>&nbsp;</div>
                            <div><b>Grado universitario: </b> ' . $user->getAcademicDegree() . '<button class="text-success" style="border:none; background:none" type="button" data-toggle="modal" data-target="#myThirdModal' . $user->getUserId() . '">
                            <i class="text-success glyphicon glyphicon-pencil" ></i>
                            </button>&nbsp; </div>
                            <div><b>Roles: </b>' . $userListComplements->translateRolesIdToText($user->getRoles()) . '</div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3">
                        <div class="h3 text-muted">Biografía <button class="text-success" style="border:none; background:none" type="button" data-toggle="modal" data-target="#myFourthModal' . $user->getUserId() . '">
                        <i class="text-success glyphicon glyphicon-pencil" ></i>
                    </button></div>
                    </div>
                    <div class="col-sm-6">' . $user->getBiography() . '</div>
                </div>        
';
        if (strpos($user->getRoles(), "16") !== false) {
           
          list(
            $completedReviews,
            $activeReviews,
            $rejectedReviews,
            $DaysSinceLastReview,
            $DaysToCompleteReviews) =  $userListHandler->queryReviewAssignments($user->getUserId());

            $table.='
            <hr>
            <div class="accordion" id="accordionReviewer'.$user->getUserId().'">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingReviewer'.$user->getUserId().'">
                    <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseReviewer'.$user->getUserId().'" aria-expanded="false" aria-controls="collapseReviewer'.$user->getUserId().'">
                    Actividad Como evaluador
                    </button>
                </h2>
                <div id="collapseReviewer'.$user->getUserId().'" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
                    <div class="accordion-body">
                    <ul class="list-group">
                    <li class="list-group-item">Revisiones completadas: '.$completedReviews.'</li>
                    <li class="list-group-item">Revisiones activas:  '.$activeReviews.'</li>
                    <li class="list-group-item">Revisiones Rechazadas: '.$rejectedReviews.'</li>
                    <li class="list-group-item">Días desde la ultima revision: '.$DaysSinceLastReview.'</li>
                    <li class="list-group-item">Promedio de días para completar una revisión: '.$DaysToCompleteReviews.'</li>
                  </ul>
                    </div>
                </div>
                </div>
          </div>';

        }
        if (strpos($user->getRoles(), "14") !== false) {
            list($publicationsSended,
            $queuedPublications,
            $publicationsAcepted,
            $publicationsRejected,
            $scheduledPublications) =  $userListHandler->queryAuthorActivity($user->getEmail());
    
                $table.='
                <hr>
                <div class="accordion" id="accordionAuthor'.$user->getUserId().'">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingReviewer'.$user->getUserId().'">
                        <button class="accordion-button collapsed" type="button" data-toggle="collapse" data-target="#collapseAuthor'.$user->getUserId().'" aria-expanded="false" aria-controls="collapseAuthor'.$user->getUserId().'">
                        Actividad Como Autor
                        </button>
                    </h2>
                    <div id="collapseAuthor'.$user->getUserId().'" class="accordion-collapse collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
                        <div class="accordion-body">
                        <ul class="list-group">
                        <li class="list-group-item">Articulos Enviados: '.$publicationsSended.'</li>
                        <li class="list-group-item">Articulos en espera:  '.$queuedPublications.'</li>
                        <li class="list-group-item">Articulos Aceptadas: '.$publicationsAcepted.'</li>
                        <li class="list-group-item">Articulos rechazados: '.$publicationsRejected.'</li>
                        <li class="list-group-item">Articulos programados: '.$scheduledPublications.'</li>
                      </ul>
                        </div>
                    </div>
                    </div>
              </div>';
    
        }

        $table .= '
        </div>
        </div>
    </div>  
<div class="modal fade" id="mySecondModal' . $user->getUserId() . '" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" id="exampleModalToggleLabel2">Actualizar universidad al usuario</h1>
        
      </div>
      <div class="modal-body">
      <form  method="POST">
      
      <input type="hidden" name="user_id" value="' . $user->getUserId() . '" style="display:none;">
      <div><b>Universidad: </b><input type="text" name="university" value="' . $user->getUniversity() . '"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" type=submit>Guardar</button>
        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myThirdModal' . $user->getUserId() . '" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" id="exampleModalToggleLabel2">Actualizar grado academico al usuario</h1>
        
      </div>
      <div class="modal-body">
      <form  method="POST">
      
      <input type="hidden" name="user_id" value="' . $user->getUserId() . '" style="display:none;">
      <div><b>Grado academico: </b><input type="text" name="newAcademicDegree" value="' . $user->getAcademicDegree() . '"></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" type=submit>Guardar</button>
        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

        </form>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="myFourthModal' . $user->getUserId() . '" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title" id="exampleModalToggleLabel2">Actualizar Biografía del usuario</h1>
        
      </div>
      <div class="modal-body">
      <form  method="POST">
      
      <input type="hidden" name="user_id" value="' . $user->getUserId() . '" style="display:none;">
      <div><b>Biografía: </b><textarea type="text" style="width: 500px; height:80px;"  name="biography" value="' . $user->getBiography() . ' ">' . $user->getBiography() . '</textarea></div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-success" type=submit>Guardar</button>
        <button class="btn btn-secondary" data-dismiss="modal">Cancelar</button>

        </form>
      </div>
    </div>
  </div>
</div>
                  ';

        return $table;
    }
}