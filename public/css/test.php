<?

?>

<!DOCTYPE html>
<html lang="ko">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0 maximum-scale=1">
  <meta property="og:type" content="website">
  <meta property="og:title" content="AI 학습모델 학습데이터 구축">
  <meta property="og:url" content="">
  <meta property="og:description" content="AI Learning Data Collecting System">
  <link rel="short cut icon" type="image/x-icon" href="../public/img/icon/favicon.png">
  <title>AI 학습모델 학습데이터 구축</title>

  <link rel="stylesheet" href="../public/css/style.css">
</head>

<body>
  <div id="wrap">
    <!-- SIDE MENU -->
    <div id="aside">
      <?
	include("./../include/menu.php");
	include_once "/inc/dbcon.php";
?>
    </div>

    <!-- PAGE CONTENT -->
    <div id="main">
      <div class="wrapper">
        <!-- 페이지 탑 -->
        <div class="page-top">
          <h2 class="page-title">작업 상세</h2>
        </div>

        <!-- 페이지 컨텐츠 섹션 // Task 정보 -->
        <section class="page-content TaskInfo">
          <div class="section__inner">
            <!-- 섹션 HEAD -->
            <div class="section--head">
              <h3 class="section-title">작업 정보</h3>
            </div>

            <?	// PHP 동적데이터 생성
	if (isset($_GET['seq'])) { // 데이터가 있는 경우
		$seq = $_GET['seq'];

		$query = "select
				CHT_SEQ, CHT_NUM, CHT_ID, CHT_ORI, CHT_CONT, CHT_TASK, CHT_LINK, CHT_TURN, CHT_ST, PUT_ID, REJ_REASON
			from
				LGAI_CHAT_DATA
			where
				CHT_SEQ='$seq'
			";
		$row = get_row2($query);
		if (!$row) {
			echo "<script>alert('잘못된 접근입니다.');
			window.location.href='./index.php';
			</script>";
		}
		//echo $row['CHT_ORI'] . "\n\n";

		// 영어원문 가져오기
		$ori = json_decode($row['CHT_ORI'], true);
		//print_r ($ori);

		$ori_val = '';
		foreach ($ori as $value) {
			if ($value['from'] == 'human') {
				$ori_val .= " 
					<li class=\"chat__item human\">
                          <div class=\"chat__inner\">
                            <p class=\"chat--subs\">
                              <span class=\"speaker human\">HUMAN</span>
				";
			} else if ($value['from'] == 'gpt') {
				$ori_val .= " 
					<li class=\"chat__item gpt\">
                          <div class=\"chat__inner\">
                            <p class=\"chat--subs\">
                              <span class=\"speaker gpt\">GPT</span>
				";
			} else {
				echo "ERROR"; exit;
			}

			$ori_val .= nl2br(htmlspecialchars($value['value'])); // 개행문자 처리
			//$ori_val .= htmlspecialchars($value['value']); // 개행문자 처리하지 않음
			$ori_val .= "
							</p>
						  </div>
						</li>
				";
		}

		// chatGPT 대화내용 가져오기
		$cont = json_decode($row['CHT_CONT'], true);
		//print_r ($cont);

		$cont_id = array_keys($cont);
		$cont_val = '';
		$cont_turn = count($cont[$cont_id[0]]['conversation']);
		//print_r ($cont[$cont_id[0]]['conversation']);

		foreach ($cont[$cont_id[0]]['conversation'] as $value) {
			if ($value['from'] == 'human') {
				$cont_val .= " 
					<li class=\"chat__item chatli human\">
                          <p class=\"chat--icon\"><span class=\"icon\"></span><span class=\"icon--cls\"</span></p>
                          <div class=\"chat__inner\">
                            <textarea class=\"chat--txt\">" . $value['value'] . "</textarea>
                          </div>
                        </li>
				";
			} else if ($value['from'] == 'gpt') {
				$cont_val .= " 
					<li class=\"chat__item chatli gpt\">
                          <p class=\"chat--icon\"><span class=\"icon\"></span><span class=\"icon--cls\"</span></p>
                          <div class=\"chat__inner\">
                            <textarea class=\"chat--txt\">" . $value['value'] . "</textarea>
                          </div>
                        </li>
				";
			} else {
				echo "ERROR"; exit;
			}
		}
		
		// 채팅 데이터 추가
		if ($cont_turn <= 12 && ($row[PUT_ID] == $_SESSION['usr_id'] || $row[PUT_ID] == '') && $row['CHT_ST'] !== 'C') {
			if ($cont_turn % 2 == 0) {
				$cont_val .= "
					<li class=\"chat__item add\">
						  <div class=\"add-chat\">
							<p class=\"chat--icon\"><span class=\"icon\"></span></p>
							<textarea name=\"\" class=\"chat--txt\" id=\"newchat\" placeholder=\"대화 내용을 입력해주세요.\"></textarea>
							<p class=\"btn btn--plus ty01\">
							  <button onclick=\"addCHAT()\">대화 추가</button>
							</p>
						  </div>
						</li>
				";
			} else {
				$cont_val .= "
					<li class=\"chat__item add\">
						  <div class=\"add-chat\">
							<p class=\"chat--icon\"><span class=\"icon\"></span></p>
							<textarea name=\"\" class=\"chat--txt\" id=\"newchat\" placeholder=\"대화 내용을 입력해주세요.\"></textarea>
							<p class=\"btn btn--plus ty01\">
							  <button onclick=\"addCHAT()\">대화 추가</button>
							</p>
						  </div>
						</li>
				";
			}
		}

	} else { // 신규 작성의 경우
		$query = "select
				CHT_NUM
			from
				LGAI_CHAT_DATA
			order by 
				CHT_NUM
			desc limit 1
			";
		$row = get_row2($query);
		$row['CHT_NUM'] += 1;
	}


?>
            <!-- 섹션 BODY -->
            <div class="section-body">
              <div class="content__wrap">
                <div class="section-content">
                  <!-- 작업 정보 // 작업자, 문서 번호 및 ID -->
                  <ul class="flex__list horizontal">
                    <li class="flex__item num--agent">
                      <span class="item-name">작업자</span>
                      <span class="item-subs"><?=$row['PUT_ID']?></span>
                    </li>
                    <li class="flex__item num--doc">
                      <span class="item-name">문서 번호</span>
                      <span class="item-subs"><?=str_pad($row['CHT_NUM'], 6, '0', STR_PAD_LEFT)?>
                        <?if(!isset($seq)) echo "(예상)";?>
                      </span>
                    </li>
                    <li class="flex__item num--id">
                      <span class="item-name">ID</span>
                      <span class="item-subs">
                        <?if(!isset($seq)) echo "(신규)"; else if ($row['CHT_ID'] == 0) echo "-"; else echo $row['CHT_ID'];?>
                      </span>
                    </li>
                  </ul>
                  <!-- Value 정보 // ChatGPT URL -->
                  <ul class="input-info__list">
                    <li class="input-info__item with--btn">
                      <span class="item-name">ChatGPT URL 입력</span>
                      <label class="input-label">
                        <input type="text" id="url" placeholder="GPT URL을 입력해 주세요."
                          value="<?=htmlspecialchars($row['CHT_LINK'])?>">
                        <p class="btn btn--input">
                          <button onclick="goURL()">입력</button>
                        </p>
                      </label>
                    </li>
                    <li class="input-info__item">
                      <span class="item-name">Task</span>
                      <label class="input-label">
                        <input type="text" id="title" placeholder="Task 내용을 입력해 주세요."
                          value="<?=htmlspecialchars($row['CHT_TASK'])?>">
                      </label>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- 페이지 컨텐츠 섹션 // Task 내용 -->
        <section class="page-content TaskContents">
          <div class="section__inner">
            <!-- 섹션 HEAD -->
            <div class="section--head">
              <h3 class="section-title">작업 내용</h3>
            </div>

            <!-- 섹션 BODY -->
            <div class="section-body chatBox">
              <div class="content__wrap">
                <!-- 대화 컨텐츠 // Original -->
                <div id="chatOrigin" class="section-content chat">
                  <!-- 대화 박스 -->
                  <div class="chat__box">
                    <div class="origin__inner">
                      <ul class="chat__list origin">
                        <?=$ori_val?>
                      </ul>
                    </div>
                  </div>
                </div>
                <!-- 대화 컨텐츠 // ChatGpt -->
                <div id="chatGpt" class="section-content chat">
                  <!-- 대화 박스 -->
                  <div class="chat__box">
                    <div class="box__inner">
                      <ul class="chat__list chatGpt">
                        <?=$cont_val?>
                        <!-- 대화 추가하기 // 6turn을 채운 경우 addClass .del ->> 노출되지 않는다. -->
                        <? if (!isset($cont_val)) { ?>
                        <li class="chat__item add human">
                          <div class="add-chat">
                            <p class="chat--icon"><span class="icon"></span></p>
                            <textarea name="" class="chat--txt" id="newchat" placeholder="대화 내용을 입력해주세요."></textarea>
                            <p class="btn btn--plus ty01">
                              <button onclick="addCHAT()">대화 추가</button>
                            </p>
                          </div>
                        </li>
                        <? } ?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- 페이지 컨텐츠 섹션 // Task 결과 -->
        <section class="page-content TaskResult">
          <div class="section__inner">
            <!-- 섹션 HEAD -->
            <div class="section--head">
              <h3 class="section-title">작업 결과</h3>
            </div>

            <!-- 섹션 BODY -->
            <div class="section-body">
              <div class="content__wrap">
                <div class="section-content">
                  <!-- 작업 결과 -->
                  <ul class="input-info__list">
                    <!-- 작성 불가 케이스 미사용 예정으로 임의 비활성화 - 2023.11.07 박정주
                    <li class="input-info__item">
                      <span class="item-name">작성 불가</span>
                      <div class="input__box hide hidable">
                        <div class="pick__wrap">
                          <div class="pick__item">
                            <h5 class="item-title">작성 불가 사유에 해당하는 항목을 선택해 주세요.</h5>
                            <p class="pick-input__wrap">
                              <label class="input-label">
                                <input type="radio" name="reasonForUnable" value="DUP">
                                <span class="label-txt">문서 중복 (동일 문서)</span>
                              </label>
                              <label class="input-label">
                                <input type="radio" name="reasonForUnable" value="SAME">
                                <span class="label-txt">앞 문서와 주제 연결 (동일 내용)</span>
                              </label>
                              <label class="input-label">
                                <input type="radio" name="reasonForUnable" value="CODE">
                                <span class="label-txt">코드 관련 주제</span>
                              </label>
                              <label class="input-label">
                                <input type="radio" name="reasonForUnable" value="BROKEN">
                                <span class="label-txt">문서 깨짐 / 내용 확인 불가</span>
                              </label>
                              <label class="input-label">
                                <input type="radio" name="reasonForUnable" value="NOVALUE">
                                <span class="label-txt">내용 없음</span>
                              </label>
                              <label class="input-label">
                                <input type="radio" name="reasonForUnable" value="reasonEtc">
                                <span class="label-txt">기타 (사유 기재 필요)</span>
                              </label>
                            </p>
                            <p class="btn btn--reset ty-danger">
                              <button id="reasonForUnableReset">reset</button>
                            </p>
                          </div>
                          <span class="input-label">
                            <textarea name="" id="writeReasonForUnable" placeholder="기타를 입력하신 경우 해당 사유를 입력해 주세요."
                              disabled></textarea>
                          </span>
                        </div>
                      </div>
                      <div class="box-opener" id="reasonForUnableOpener">
                        <label class="input-label">
                          <textarea name="" placeholder="작성 불가 사유를 선택해주세요."></textarea>
                        </label>
                      </div>
                    </li>
					-->
                    <? if (isset($seq) && $row['CHT_ST'] !== 'N') { ?>
                    <li class="input-info__item">
                      <span class="item-name">반려 사유</span>
                      <div class="input__box">
                        <span class="input-label">
                          <textarea name="" id="reasonForReject"
                            placeholder="반려 사유를 입력해 주세요."><?=$row['REJ_REASON']?></textarea>
                        </span>
                    </li>
                    <? } ?>
                  </ul>
                  <!-- 작업 결과 버튼 -->
                  <div class="btn__wrap">
                    <? if (isset($seq)) { ?>
                    <? if ($row[PUT_ID] == $_SESSION['usr_id']) { ?>
                    <p class="btn ty01 wide"><button id="saveBtn" onclick="doSave()">수정</button></p>
                    <? } else if ($row[PUT_ID] == '') { ?>
                    <p class="btn ty01 wide"><button id="saveBtn" onclick="doSave()">저장</button></p>
                    <? } ?>
                    <? if ($_SESSION["auth"] !== "worker" && $row['CHT_ST'] !== 'N') { ?>
                    <p class="btn ty-danger wide"><button onclick="doReject()">반려</button></p>
                    <p class="btn ty01 wide"><button onclick="doConfirm()">검수완료</button></p>
                    <? } ?>
                    <? } else { ?>
                    <p class="btn ty01 wide"><button id="saveBtn" onclick="doSave()">저장</button></p>
                    <? } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </div>

    <!-- 로딩 애니메이션 >> layout.css // addClass active로 동작, 종료의 경우 removeClass active -->
    <div id="loading">
      <div class="loading-bg">
        <p class="loading-title">대화를 불러 오고 있습니다.</p>
      </div>
      <div class="dots-flow"></div>
    </div>

    <!-- FOOTER -->
    <div id="footer"></div>
  </div>
  <script src="../public/js/jquery-3.7.1.js"></script>
  <script src="../public/js/include.js"></script>
  <script src="../public/js/main.js"></script>
  <script>
  function autoResize() {
    /** 작업내용 // chatGpt와의 대화에서 입력되는 줄 수만큼 textarea의 높이 조절 // START **/
    const chatTxts = document.querySelectorAll(`.chat--txt`);

    for (i = 0; i < chatTxts.length; i++) {
      if (chatTxts[i].scrollHeight > chatTxts[i].clientHeight) //textarea height 확장
        chatTxts[i].style.height = chatTxts[i].scrollHeight + "px";
      else //textarea height 축소
        chatTxts[i].style.height = (chatTxts[i].scrollHeight - 18) + "px";
    }

    function autoResizeTextarea() {
      for (i = 0; i < chatTxts.length; i++) {
        if (chatTxts[i]) {
          chatTxts[i].style.height = 'auto';
          let height = chatTxts[i].scrollHeight; // 높이
          chatTxts[i].style.height = `${height + 2}px`;
        }
      }
    };
    for (i = 0; i < chatTxts.length; i++) {
      chatTxts[i].addEventListener('keydown', autoResizeTextarea);
      //chatTxts[i].addEventListener('keyup', autoResizeTextarea);
    };
    /** 작업내용 // chatGpt와의 대화에서 입력되는 줄 수만큼 textarea의 높이 조절 // END **/

  }

  function showLoading() {
    const loadingElement = document.getElementById("loading");
    loadingElement.classList.add("active");
  }

  function hideLoading() {
    const loadingElement = document.getElementById("loading");
    loadingElement.classList.remove("active");
  }

  function goURL() {
    var url = document.getElementById("url").value;
    // var urlRegex = /^(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}([a-zA-Z0-9-._~:/?#[\]@!$&'()*+,;=]*)?$/; // 일반 URL 형식
    var urlRegex = /^(http(s)?:\/\/)?chat.openai.com\/share([a-zA-Z0-9-._~:\/?#[\]@!$&'()*+,;=]*)?$/; // CHAT GPT URL 형식

    if (url == '') {
      alert("GPT URL이 입력되지 않았습니다. URL을 입력해 주세요.");
    } else {

      if (urlRegex.test(url)) {
        showLoading();
        $.ajax({
          type: 'POST',
          url: './chat_geturl.php',
          data: {
            gpturl: url,
          },
          timeout: 100000,
          success: function(data) {
            var result = data.result;
            var output = data.val;

            if (result === 'Y') {
              //alert("대화 데이터를 성공적으로 가져왔습니다.");
              //console.log(JSON.stringify(output));

              if (output) {
                var jsonData = JSON.parse(JSON.stringify(output));

                if (jsonData) {
                  var title = jsonData['props']['pageProps']['serverResponse']['data']['title'];
                  console.log('● chatGPT Task : ' + title);
                  var titleElement = document.getElementById('title');
                  titleElement.value = title;
                  //titleElement.readOnly = true;

                  var chatGptbox = document.querySelector(".chatGpt");
                  chatGptbox.innerHTML = '';
                  var content = '';

                  let turncnt = 0;
                  for (var i = 0; i < jsonData['props']['pageProps']['serverResponse']['data'][
                      'linear_conversation'
                    ].length; i++) {

                    if (jsonData['props']['pageProps']['serverResponse']['data']['linear_conversation'][i]
                      .hasOwnProperty("message") && jsonData['props']['pageProps']['serverResponse']['data'][
                        'linear_conversation'
                      ][i]['message']['author']['role'] !== 'system') {

                      var author = jsonData['props']['pageProps']['serverResponse']['data']['linear_conversation']
                        [i]['message']['author']['role'];
                      if (author === 'user') {
                        role = 'human';
                      } else if (author === 'assistant') {
                        role = 'gpt';
                      } else {
                        alert("역할 구분을 불러올 수 없습니다. 관리자에게 문의하세요.");
                        exit;
                      }

                      turncnt++;
                      var talk = jsonData['props']['pageProps']['serverResponse']['data']['linear_conversation'][
                        i
                      ]['message']['content']['parts'][0];
                      console.log('[' + i + '] ' + turncnt + '. ' + role + ' : ' + talk);
                      //console.log ("대화 내용 있음!");

                      if (turncnt <= 12) {
                        content += '<li class="chat__item chatli ' + role +
                          '"><p class="chat--icon"><span class="icon"></span></p><div class="chat__inner"><textarea class="chat--txt">' +
                          talk + '</textarea></div></li>';
                      }

                    } else {
                      //console.log ("대화 내용 없음.");
                    }

                  }

                  if (turncnt > 12) {
                    alert("대화 Turn 12를 초과하였습니다. 대화 데이터 일부만 반영됩니다.");
                  } else {
                    alert("대화 데이터를 성공적으로 가져왔습니다.");
                  }

                  chatGptbox.innerHTML = content;
                  autoResize();

                } else {
                  alert("JSON 파싱 실패. 관리자에게 문의하세요.");
                }
              } else {
                alert("스크립트 태그를 찾을 수 없습니다. 관리자에게 문의하세요.");
              }
            } else {
              alert("대화 데이터 반환 결과가 없습니다. URL을 다시한번 확인해 주세요.");
            }
            hideLoading();
          },
          error: function(jqXHR, textStatus, errorThrown) {
            if (textStatus === "timeout") {
              alert("오류 : 응답 시간 초과");
            } else {
              alert("오류 : " + errorThrown);
              console.log(jqXHR); // jqXHR 객체를 콘솔에 출력하여 상세한 정보 확인 가능
            }
            hideLoading();
          },
          dataType: 'json'
        });
      } else {
        alert("GPT URL이 유효하지 않습니다. URL을 확인해 주세요.");
      }
    }
  }

  function addCHAT() {
    var newchat = document.getElementById("newchat").value;
    // var urlRegex = /^(http(s)?:\/\/)?(www\.)?[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}([a-zA-Z0-9-._~:/?#[\]@!$&'()*+,;=]*)?$/; // 일반 URL 형식
    var chatRegex = /\S{2,}/; // 공백이 아닌 2글자 이상인 경우 검증

    if (newchat == '') {
      alert("대화가 입력되지 않았습니다. 대화 내용을 입력해 주세요.");
    } else {

      if (chatRegex.test(newchat)) {
        //alert("대화 통과.");
        var chatElement = document.querySelector('.chatGpt');
        //var chatCount = $('.chat__item').length;
        var chatCount = chatElement.querySelectorAll('.chatli').length;
        //alert(chatCount);

        if (chatCount % 2 == 0) {
          role = 'human';
          $('.chat__item.add').removeClass().addClass('chat__item add gpt');
        } else if (chatCount % 2 == 1) {
          role = 'gpt';
          $('.chat__item.add').removeClass().addClass('chat__item add human');
        } else {
          alert("역할 구분을 불러올 수 없습니다. 관리자에게 문의하세요.");
          return;
        }
        var chatGptbox = document.querySelector(".chatGpt");
        if (chatCount <= 11) {
          var newchatHTML = '<li class="chat__item chatli ' + role +
            '"><p class="chat--icon"><span class="icon"></span></p><div class="chat__inner"><textarea class="chat--txt">' +
            newchat + '</textarea></div></li>';
          chatGptbox.insertAdjacentHTML("beforeend", newchatHTML);
          document.getElementById("newchat").value = '';
          document.getElementById("newchat").style.height = '26px';

          if (typeof boxHeight === 'undefined') {
            boxHeight = 0;
          }
          boxHeight += $(".box__inner").height() + 400;
          $(".box__inner").animate({
            scrollTop: boxHeight
          }, 500);
          autoResize();

        } else {
          alert("대화 Turn 12를 초과하였습니다. 입력한 대화는 반영되지 않습니다.");
        }

      } else {
        alert("대화 내용이 유효하지 않습니다.");
      }
    }
  }

  function tossData(action) {

    if (action == 'able') { // 작성완료  

      // seq, url, task 값 로드
      var seq = <? echo(!empty($seq) ? $seq : '""'); ?> ;
      var url = document.getElementById("url").value;
      var task = document.getElementById("title").value;

      // 대화 값 로드    
      var conversation = [];
      const gptAreas = document.querySelector('.chatGpt');
      const chatAreas = gptAreas.querySelectorAll('.chatli .chat--txt');

      chatAreas.forEach(function(textarea, index) {
        var chatContent = textarea.value;
        //console.log(`대화 Turn ${index + 1}: ${chatContent}`);
        conversation.push(chatContent);
      });
      console.log("변환전: " + conversation);
      var jsonConversation = JSON.stringify(conversation);
      console.log("변환후: " + jsonConversation);

      $.ajax({
        type: 'POST',
        url: './chat_proc.php',
        data: {
          action: action,
          seq: seq,
          url: url,
          title: task,
          conversation: conversation,
        },
        timeout: 300000,
        success: function(data) {
          var result = data.result;
          var msg = data.msg;

          if (result === 'Y') {
            alert("저장 되었습니다!");
            location.replace('./chat_list.php');
          } else {
            alert(msg);
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          if (textStatus === "timeout") {
            alert("오류 : 응답 시간 초과");
          } else {
            alert("오류 : " + errorThrown);
            console.log(jqXHR); // jqXHR 객체를 콘솔에 출력하여 상세한 정보 확인 가능
          }
        },
        dataType: 'json'
      });

    } else if (action == 'unable') { // 작성불가

      // seq, 사유 값 로드
      var seq = <? echo(!empty($seq) ? $seq : '""'); ?> ;
      var reasonRadio = document.querySelector('input[name="reasonForUnable"]:checked');
      var reasonForUnable = reasonRadio.value;
      if (reasonForUnable == 'reasonEtc') {
        var reasonForUnable = document.getElementById("writeReasonForUnable").value;
      } 

      $.ajax({
        type: 'POST',
        url: './chat_proc.php',
        data: {
          action: action,
          seq: seq,
          reason: reasonForUnable,
        },
        timeout: 300000,
        success: function(data) {
          var result = data.result;
          var msg = data.msg;

          if (result === 'Y') {
            alert("저장 되었습니다.");
          } else {
            alert("반환 결과가 없습니다. 관리자에게 문의하세요.");
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          if (textStatus === "timeout") {
            alert("오류 : 응답 시간 초과");
          } else {
            alert("오류 : " + errorThrown);
            console.log(jqXHR); // jqXHR 객체를 콘솔에 출력하여 상세한 정보 확인 가능
          }
        },
        dataType: 'json'
      });
    } else {
      alert("요청 구분을 불러올 수 없습니다. 관리자에게 문의하세요.");
      return;
    }
  }

  function doSave() {

    // 작성불가사유 확인
    var reasonRadio = document.querySelector('input[name="reasonForUnable"]:checked');

    // TASK 확인 (작성불가사유 있을 시 pass)
    var task = document.getElementById("title").value;
    var chatRegex = /\S{2,}/; // 공백이 아닌 2글자 이상인 경우 검증

    if (task == '' && !reasonRadio) {
      alert("Task가 입력되지 않았습니다. 대화 요약 Task를 입력해 주세요.");
      return;
    } else {
      if (chatRegex.test(task)) {
        // pass
      } else if (reasonRadio) {
        // pass
      } else {
        alert("Task가 유효하지 않습니다.");
        return;
      }
    }

    var chatElement = document.querySelector('.chatGpt');
    //var ulElement = document.querySelector('.ulc');
    //var chatCount = $('.chat__item').length;
    var chatCount = chatElement.querySelectorAll('.chatli').length;

    // 작성가능/작성불가 구분
    if (reasonRadio) { // 작성불가 시
      if (reasonRadio.value == 'reasonEtc') { // 작성불가 사유 : 기타 선택시

        var reasonText = document.getElementById("writeReasonForUnable").value;
        if (reasonText == "") { // 기타 사유 미입력
          alert("기타 선택 - 작성 불가 사유를 기재해 주세요.");
          return;
        } else if (['DUP', 'SAME', 'CODE', 'BROKEN', 'NOVALUE', 'reasonEtc'].includes(reasonText.toUpperCase())) {
          alert("해당 사유는 사용 불가능합니다.");
          return;
        } else {
          //alert("pass, 작성불가 : 기타 - " + reasonText);
          tossData("unable");
        }
      } else { // 작성불가 사유 : 기타 외 선택 시
        //alert("pass, 작성불가 : " + reasonRadio.value);
        tossData("unable");
      }

    } else { // 작성가능 시
      // 대화 미입력, 대화 Pair 체크
      if (chatCount == 0) {
        alert("대화가 입력되지 않았습니다. 대화 내용을 입력해 주세요.");
        return;
      } else if (chatCount % 2 == 1) {
        alert("대화 Pair가 맞지 않습니다. 대화 내역을 확인해주세요.");
        return;
      } else if (chatCount % 2 == 0) {

        // 대화 Turn 체크
        if (chatCount < 8) {
          alert("대화 Turn을 8 이상 입력해주세요.");
          return;
        } else {
          //alert("pass, 작성가능 : Turn 8 이상임");
          tossData("able");
        }

      } else {
        alert("대화 영역을 불러올 수 없습니다. 관리자에게 문의하세요.");
        return;
      }
    }
  }

  function doReject() {
    var rejectText = document.getElementById("reasonForReject").value;
    var reasonRegex = /\S{2,}/; // 공백이 아닌 2글자 이상인 경우 검증

    if (rejectText == '') {
      alert("반려 사유가 입력되지 않았습니다. 반려 사유를 입력해 주세요.");
    } else {

      if (reasonRegex.test(rejectText)) {
        //alert("대화 통과.");
        var chk = window.confirm("작성하신 사유로 반려처리 하시겠어요?");
        if (chk) {
          // seq, 사유 값 로드
          var seq = < ? echo(!empty($seq) ? $seq : '""'); ? > ;
          if (seq == '') {
            alert("시퀀스 값이 없습니다. 관리자에게 문의하세요.");
            return;
          }
          var action = 'reject';

          $.ajax({
            type: 'POST',
            url: './chat_proc.php',
            data: {
              action: action,
              seq: seq,
              reason: rejectText,
            },
            timeout: 300000,
            success: function(data) {
              var result = data.result;
              var msg = data.msg;

              if (result === 'Y') {
                alert("반려 처리 되었습니다.");
                location.replace('./chat_list.php');
              } else {
                alert("반환 결과가 없습니다. 관리자에게 문의하세요.");
              }
            },
            error: function(jqXHR, textStatus, errorThrown) {
              if (textStatus === "timeout") {
                alert("오류 : 응답 시간 초과");
              } else {
                alert("오류 : " + errorThrown);
                console.log(jqXHR); // jqXHR 객체를 콘솔에 출력하여 상세한 정보 확인 가능
              }
            },
            dataType: 'json'
          });
        }

      } else {
        alert("반려 사유를 상세하게 작성해주세요.");
      }
    }
  }

  function doConfirm() {
    var chk = window.confirm("검수완료 처리 하시겠어요?");
    if (chk) {
      // seq, 사유 값 로드
      var seq = < ? echo(!empty($seq) ? $seq : '""'); ? > ;
      if (seq == '') {
        alert("시퀀스 값이 없습니다. 관리자에게 문의하세요.");
        return;
      }
      var action = 'confirm';

      $.ajax({
        type: 'POST',
        url: './chat_proc.php',
        data: {
          action: action,
          seq: seq,
        },
        timeout: 300000,
        success: function(data) {
          var result = data.result;
          var msg = data.msg;

          if (result === 'Y') {
            alert("검수완료 처리 되었습니다.");
            location.replace('./chat_list.php');
          } else {
            alert("반환 결과가 없습니다. 관리자에게 문의하세요.");
          }
        },
        error: function(jqXHR, textStatus, errorThrown) {
          if (textStatus === "timeout") {
            alert("오류 : 응답 시간 초과");
          } else {
            alert("오류 : " + errorThrown);
            console.log(jqXHR); // jqXHR 객체를 콘솔에 출력하여 상세한 정보 확인 가능
          }
        },
        dataType: 'json'
      });
    }
  }

  $(document).ready(function() {

    autoResize();

    /** 작업내용 // 작성불가사유 중 기타를 선택할 시 하단의 textarea 활성화 // START **/
    const radioButtons = document.querySelectorAll('input[name="reasonForUnable"]');
    const textareaEtc = document.getElementById('writeReasonForUnable');

    for (const radio of radioButtons) {
      radio.addEventListener('change', function() {
        saveBtn.textContent = '작성불가';
        if (radio.value === 'reasonEtc') {
          textareaEtc.disabled = false; // 텍스트 에어리어 활성화
        } else {
          textareaEtc.value = '';
          textareaEtc.disabled = true; // 텍스트 에어리어 비활성화
        }
      });
    }
    /** 작업내용 // 작성불가사유 중 기타를 선택할 시 하단의 textarea 활성화 // END **/

    /** 작업내용 // 작성불가사유 선택 후 모든 선택 해제하기 // START **/
    $('#reasonForUnableOpener').on('click', function() {
      $(this).addClass('hide');
      $('.hidable').removeClass('hide');
    });

    $('#reasonForUnableReset').on('click', function() {
      $('input[name="reasonForUnable"]').prop('checked', false);
      textareaEtc.value = '';
      textareaEtc.disabled = true; // 텍스트 에어리어 비활성화
      saveBtn.textContent = '저장';
    });
    /** 작업내용 // 작성불가사유 선택 후 모든 선택 해제하기 // END **/

  });
  </script>
</body>

</html>